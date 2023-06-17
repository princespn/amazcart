<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Wallet\Entities\WalletBalance;
use Modules\Customer\Entities\CustomerAddress;
use Modules\Marketing\Entities\CouponUse;
use Modules\GST\Entities\OrderPackageGST;
use Modules\GiftCard\Entities\GiftCardUse;
use Modules\OrderManage\Entities\CancelReason;
use Carbon\Carbon;
use Modules\Affiliate\Entities\AffiliateReferralPayment;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(User::class,'customer_id','id');
    }

    public function cancel_reason(){
        return $this->belongsTo(CancelReason::class,'cancel_reason_id','id');
    }

    public function packages(){
        return $this->hasMany(OrderPackageDetail::class,'order_id','id');
    }

    public function gift_card_uses(){
        return $this->hasMany(GiftCardUse::class,'order_id','id');
    }

    public function guest_info()
    {
        return $this->hasOne(GuestOrderDetail::class,'order_id','id');
    }

    public function order_payment(){
        return $this->belongsTo(OrderPayment::class,'order_payment_id', 'id');
    }

    public function shipping_address(){
        return $this->belongsTo(CustomerAddress::class, 'customer_shipping_address','id');
    }

    public function billing_address(){
        return $this->belongsTo(CustomerAddress::class, 'customer_billing_address','id');
    }
    public function address(){
        return $this->hasOne(OrderAddressDetail::class,'order_id', 'id');
    }

    public function wallets()
    {
        return $this->morphMany(WalletBalance::class, 'walletable');
    }
    public function coupon(){
        return $this->hasOne(CouponUse::class,'order_id','id');
    }

    public function getGatewayNameAttribute()
    {
        switch ($this->payment_type) {
            case '1':
                return __("payment_gatways.cash_on_delivery");
                break;
            case '2':
                return __("payment_gatways.wallet");
                break;
            case '3':
                return __("payment_gatways.paypal");
                break;
            case '4':
                return __("payment_gatways.stripe");
                break;
            case '5':
                return __("payment_gatways.paystack");
                break;
            case '6':
                return __("payment_gatways.razorpay");
                break;
            case '7':
                return __("payment_gatways.bank_payment");
                break;
            case '8':
                return __("payment_gatways.instamojo");
                break;
            case '9':
                return __("payment_gatways.paytm");
                break;
            case '10':
                return __("payment_gatways.midtrans");
                break;
            case '11':
                return __("payment_gatways.payumoney");
                break;
            case '12':
                return __("payment_gatways.jazzcash");
                break;
            case '13':
                return __("payment_gatways.google_pay");
                break;
            case '14':
                return __("payment_gatways.flutter_wave_payment");
                break;
            case '15':
                return __("payment_gatways.bkash");
                break;
            case '16':
                return __("payment_gatways.ssl_commerz");
                break;
            case '17':
                return __("payment_gatways.mercado_pago");
                break;
            case 'BankPayment':
                return __("payment_gatways.bank_payment");
                break;
            default:
                return $this->payment_method;
                break;
        }
    }

    public function getTotalGstAmountAttribute()
    {
        $order_id = $this->id;
        return OrderPackageGST::whereHas('order_package', function($q) use($order_id){
            $q->where('order_id', $order_id);
        })->get()->sum('amount');
    }

    public function scopeTotalSaleCount($query, $type)
    {
        $year = Carbon::now()->year;
        if ($type == "today") {
            return $query->whereBetween('created_at', [Carbon::now()->format('y-m-d')." 00:00:00", Carbon::now()->format('y-m-d')." 23:59:59"])->sum('grand_total');
        }
        if ($type == "week") {
            return $query->whereBetween('created_at', [Carbon::now()->subDays(7)->format('y-m-d')." 00:00:00", Carbon::now()->format('y-m-d')." 23:59:59"])->sum('grand_total');
        }
        if ($type == "month") {
            $month = Carbon::now()->month;
            $date_1 = Carbon::create($year, $month)->startOfMonth()->format('Y-m-d')." 00:00:00";
            return $query->whereBetween('created_at', [$date_1, Carbon::now()->format('y-m-d')." 23:59:59"])->sum('grand_total');
        }
        if ($type == "year") {
            $date_1 = Carbon::create($year, 1)->startOfMonth()->format('Y-m-d')." 00:00:00";
            return $query->whereBetween('created_at', [$date_1, Carbon::now()->format('y-m-d')." 23:59:59"])->sum('grand_total');
        }

    }

    public function scopeOrderInfo($query, $type, $state)
    {
        $year = Carbon::now()->year;
        if ($type == "today") {
            $query->whereBetween('created_at', [Carbon::now()->format('y-m-d')." 00:00:00", Carbon::now()->format('y-m-d')." 23:59:59"]);
        }
        elseif ($type == "week") {
            $query->whereBetween('created_at', [Carbon::now()->subDays(7)->format('y-m-d')." 00:00:00", Carbon::now()->format('y-m-d')." 23:59:59"]);
        }
        elseif ($type == "month") {
            $month = Carbon::now()->month;
            $date_1 = Carbon::create($year, $month)->startOfMonth()->format('Y-m-d')." 00:00:00";
            $query->whereBetween('created_at', [$date_1, Carbon::now()->format('y-m-d')." 23:59:59"]);
        }
        elseif ($type == "year") {
            $date_1 = Carbon::create($year, 1)->startOfMonth()->format('Y-m-d')." 00:00:00";
            $query->whereBetween('created_at', [$date_1, Carbon::now()->format('y-m-d')." 23:59:59"]);
        }

        if ($state === "all") {
            return $query->count();
        }
        elseif ($state === 0) {
            return $query->where('is_confirmed', 0)->count();
        }
        elseif ($state === 1) {
            return $query->where('is_completed', 1)->count();
        }

    }

    public function affiliateUser(){
        return $this->hasOne(AffiliateReferralPayment::class,'order_id','id');
    }
    public function affiliatePayments(){
        return $this->hasMany(AffiliateReferralPayment::class,'order_id','id');
    }
}
