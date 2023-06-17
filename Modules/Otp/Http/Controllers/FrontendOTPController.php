<?php

namespace Modules\Otp\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Contracts\Support\Renderable;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Traits\Otp as TraitsOtp;
use App\Traits\SendMail;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Otp\Entities\Otp;
use Modules\UserActivityLog\Traits\LogActivity;
use Illuminate\Foundation\Auth\RegistersUsers;
use Carbon\Carbon;
use Exception;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class FrontendOTPController extends Controller
{

    use RegistersUsers, TraitsOtp,SendMail;

    protected function redirectTo()
    {
        if (auth()->user()->role->type == 'superadmin' || auth()->user()->role->type == 'admin' || auth()->user()->role->type == 'staff') {
            return '/admin-dashboard';
        } elseif (auth()->user()->role->type == 'seller') {
            return '/seller/dashboard';
        }
        return '/';
    }
    public function otp_check(Request $request)
    {
        try {
            $otp = Session::get('otp');
            $validation_time = Session::get('validation_time');

            if ($otp != $request->otp) {
                Toastr::error(__('otp.invalid_otp'));
                Session::put('code_validation_time',$request->code_validation_time);
                return view(theme('auth.otp'), compact('request'));
            } elseif (date('Y-m-d H:i:s') > $validation_time) {
                Session::put('code_validation_time',1);
                Toastr::error(__('otp.otp_validation_time_expired'));
                return view(theme('auth.otp'), compact('request'));
            } else {

                Session::forget('otp');
                Session::forget('validation_time');
                Session::forget('code_validation_time');

                event(new Registered($user =  (new RegisterController())->create($request)));
                $user->update(['is_verified' => 1]);
                $this->guard()->login($user);

                Toastr::success(__('auth.successfully_registered'), __('common.success'));
                LogActivity::addLoginLog(Auth::user()->id, Auth::user()->first_name . ' - logged in at : ' . Carbon::now());
                return $this->registered($request, $user)
                    ?: redirect($this->redirectPath());
            }
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->route('register');
        }
    }

    public function login_otp_check(Request $request)
    {
        try {
            $otp = Session::get('otp');
            $validation_time = Session::get('validation_time');

            if ($otp != $request->otp) {
                Toastr::error(__('otp.invalid_otp'));
                Session::put('code_validation_time',$request->code_validation_time);
                return view(theme('auth.login_otp'), compact('request'));
            } elseif (date('Y-m-d H:i:s') > $validation_time) {
                Session::put('code_validation_time',1);
                Toastr::error(__('otp.otp_validation_time_expired'));
                return view(theme('auth.login_otp'), compact('request'));
            } else {

                Session::forget('otp');
                Session::forget('validation_time');
                Session::forget('code_validation_time');

                $loginCon = new LoginController();
                return $loginCon->loginDone($request);
            }
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->route('login');
        }
    }


    public function resend_otp(Request $request)
    {
        try {
            if($this->sendOtp($request, "resend")){
                Toastr::success(__('otp.otp_send_successful'), __('common.success'));
            }else{
                Toastr::error(__('otp.something_wrong_on_otp_send'), __('common.error'));
            }
            return view(theme('auth.otp'), compact('request'));
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->route('register');
        }
    }

    public function resend_login_otp(Request $request)
    {
        try {
            if($this->sendLoginOtp($request, "resend")){
                Toastr::success(__('otp.otp_send_successful'), __('common.success'));
            }else{
                Toastr::error(__('otp.something_wrong_on_otp_send'), __('common.error'));
            }
            return view(theme('auth.login_otp'), compact('request'));
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->route('login');
        }
    }


    public function resend_otp_for_seller(Request $request)
    {
        try {
            if($this->sendOtpForSeller($request, "resend")){
                Toastr::success(__('otp.otp_send_successful'), __('common.success'));
            }else{
                Toastr::error(__('otp.something_wrong_on_otp_send'), __('common.error'));
            }
            return view(theme('auth.otp_seller'), compact('request'));
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->route('frontend.merchant-register-step-first');
        }
    }

    public function order_otp_check(Request $request)
    {

        try {
             $otp = Session::get('otp');
            $validation_time = Session::get('validation_time');

            if ($otp != $request->otp) {
                Toastr::error(__('otp.invalid_otp'));
                Session::put('code_validation_time',$request->code_validation_time);
                return view(theme('pages.order_otp'), compact('request'));


            } elseif (date('Y-m-d H:i:s') > $validation_time) {
                Session::put('code_validation_time',1);
                Toastr::error(__('otp.otp_validation_time_expired'));
                return view(theme('pages.order_otp'), compact('request'));
            } else {

                Session::forget('otp');
                Session::forget('code_validation_time');
                Session::forget('validation_time');
                DB::beginTransaction();

                $order = (new OrderRepository())->orderStore(Session::get('request'));

                DB::commit();
                if (app('business_settings')->where('type', 'mail_notification')->first()->status == 1) {
                    $this->sendInvoiceMail($order->order_number, $order);
                }
                Toastr::success(__('order.oredre_created_successfully'), __('common.success'));
                LogActivity::successLog('order store successful.');
                return redirect()->route('frontend.order.summary_after_checkout', encrypt($order->id));
            }
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->back();
        }
    }

    public function order_resend_otp()
    {
        $request = (object) Session::get('request');
        try {
            if($this->sendOtpForOrder($request, "resend")){
                Toastr::success(__('otp.otp_send_successful'), __('common.success'));
            }else{
                Toastr::error(__('otp.something_wrong_on_otp_send'), __('common.error'));
            }
            return view(theme('pages.order_otp'), compact('request'));
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->back();
        }
    }

    public function send_password_reset_otp(Request $request){
        $request->validate([
            'email' => 'required'
        ]);
        if (is_numeric($request->email)) {
            $request->validate([
                'email' => 'required|exists:users,phone'
            ]);
        }else{
            $request->validate([
                'email' => 'required|exists:users,email'
            ]);
        }
        try {
            if (!$this->sendPasswordResetOtp($request)) {
                Toastr::error(__('otp.something_wrong_on_otp_send'), __('common.error'));
                return back();
            }
            return view(theme('auth.password_reset_otp'), compact('request'));
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('otp.something_wrong_on_otp_send'), __('common.error'));
            return back();
        }
    }

    public function resend_password_reset_otp(Request $request){
        try {
            if($this->sendPasswordResetOtp($request, "resend")){
                Toastr::success(__('otp.otp_send_successful'), __('common.success'));
            }else{
                Toastr::error(__('otp.something_wrong_on_otp_send'), __('common.error'));
            }
            return view(theme('auth.password_reset_otp'), compact('request'));
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->route('login');
        }
    }

    public function password_reset_otp_check(Request $request)
    {
        $otp = Session::get('otp');
            $validation_time = Session::get('validation_time');

            if ($otp != $request->otp) {
                Toastr::error(__('otp.invalid_otp'));
                Session::put('code_validation_time',$request->code_validation_time);
                return view(theme('auth.password_reset_otp'), compact('request'));
            } elseif (date('Y-m-d H:i:s') > $validation_time) {
                Session::put('code_validation_time',1);
                Toastr::error(__('otp.otp_validation_time_expired'));
                return view(theme('auth.password_reset_otp'), compact('request'));
            } else {

                Session::forget('otp');
                Session::forget('validation_time');
                Session::forget('code_validation_time');
                Session::forget('password_reset_by_otp_user');

                $user = null;
                if (is_numeric($request->email)) {
                    $request->validate([
                        'email' => 'required|exists:users,phone'
                    ]);
                    $user = User::where('phone', $request->email)->first()->id;

                }else{
                    $request->validate([
                        'email' => 'required|exists:users,email'
                    ]);
                    $user = User::where('email', $request->email)->first()->id;
                }

                if($user != null){
                    Session::put('password_reset_by_otp_user', $user);
                    return view(theme('auth.password_reset_form_otp'));
                }
                Toastr::error('User Not Found');
                return redirect(url('/password/reset'));
                
            }

        try {
            
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.error_message'), __('common.error'));
            return redirect()->route('login');
        }
    }

    public function otp_user_password_update(Request $request){
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);
        if(Session::has('password_reset_by_otp_user')){
            $user = User::find(Session::get('password_reset_by_otp_user'));
            if($user){
                $user->password = Hash::make($request->password);
                $user->save();
                Toastr::success(__('common.updated_successfully'),__('common.success'));
                return redirect(url('/'));
            }
            Toastr::error('User Not Found');
                return redirect(url('/password/reset'));
        }
        Toastr::error('User Not Found');
        return redirect(url('/password/reset'));
    }
}

