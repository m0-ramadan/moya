<?php

namespace App\Services;

use App\Models\City;
use App\Models\Order;
use App\Models\Client;
use App\Models\Country;
use Paytabscom\Laravel_paytabs\Facades\paypage;


class PaytabsService
{
    private $payment_method = 'all';
    private $transaction_type = 'sale';

    private $order;
    private $customer;

    private $city;
    private $country;

    private $return;
    private $callback;
    private $language = 'ar'; // ar/en


    public function __construct(Order $order, $client_data)
    {
        $this->order = $order;
        $this->customer = $client_data;
        $this->return = route('redirect', ['codeId' => $this->order->code_order]);
        $this->callback = route('callback');
        $this->city = City::find($this->customer['state_id']);
        $this->country = Country::find($this->customer['country_id']);
    }

    public function pay()
    {
        $pay = paypage::sendPaymentCode($this->payment_method)
            ->sendTransaction($this->transaction_type, 'ecom')
            ->sendCart($this->order->id, $this->order->sub_total, 'cart description')
            ->sendCustomerDetails(
                $this->customer['name'],
                $this->customer['email'],
                $this->customer['phone'],
                $this->customer['address'], // street
                $this->city->name,
                $this->city->getTranslation('name', 'en'),
                $this->order->country_ref_code,
                $this->customer['zip_code'],
                $this->customer['id'],
            )
            // ->sendShippingDetails(
            //     $this->customer['name'],
            //     $this->customer['email'],
            //     $this->customer['phone'],
            //     $this->customer['address'], // street
            //     $this->city->name,
            //     $this->city->getTranslation('name', 'en'),
            //     $this->order->country_ref_code,
            //     $this->customer['zip_code'],
            //     $this->customer['id'],
            //     true
            //     )
            ->shipping_same_billing()
            ->sendHideShipping(true)
            // ->sendURLs(null, $this->callback)
            ->sendURLs($this->return, $this->callback)
            ->sendLanguage($this->language)
            ->sendFramed(false)
            ->create_pay_page();

        return $pay;
    }
}
