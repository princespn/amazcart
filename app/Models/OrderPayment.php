<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PaymentGateway\Entities\PaymentMethod;
use Modules\Wallet\Entities\BankPayment;

class OrderPayment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function item_details()
    {
        return $this->morphOne(BankPayment::class, 'itemable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function method()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method');
    }

    public function order(){
        return $this->hasOne(Order::class, 'order_payment_id', 'id');
    }

    public function getGatewayNameAttribute()
    {
        switch ($this->payment_method) {
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
}
