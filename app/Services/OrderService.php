<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\City;
use App\Models\Bouns;
use App\Models\Order;
use App\Models\Country;
use App\Models\OrderItem;
use App\Models\Coupons;
use App\Models\BounsPoint;
use App\Models\GiftWallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderService
{

    public $cart_items;
    public $order;
    private $delivery_cost = 0;
    private $cart_cost;
    private $user;
    private $data;
    private $sub_total;
    private $total;
    private $discount;
 private $cashback;
    public function __construct($data = null, $user, $ip = null, $discount = null,$cashback)
    {
        $this->cashback=$cashback;       
        $this->ip = $ip;
        $this->data = $data;
           $this->discount = $discount;

        $this->user = $user;
        $cart = $user ? Cart::where('client_id', $this->user->id)->with('items.product')->latest()->first()
                        : Cart::where('ip_address', $ip)->with('items.product')->latest()->first();
        $this->cart_items = $cart->items;
    }

    public function calcTotalCartPrice()
    {
        if (!$this->cart_items->count()) {
            throw new NotFoundHttpException;
        }

        $this->cart_cost = $this->cart_items->map(function ($cart_item) {
            return $cart_item->quantity * $cart_item->product->current_price;
        })->sum();

        return $this;
    }

    public function calcOrderTotal()
    {
        return $this->cart_cost + $this->sub_total;
    }

    private function generateOrderNumber($n = 1)
    {
        $number = $n * mt_rand(1, 9999);

        if (Order::where('code_order', $number)->exists()) {
            return $this->generateOrderNumber($n + 1);
        }

        return $number;
    }

    public function calcOrderSubTotal()
    {
        $this->sub_total = $this->cart_cost;
        $this->total = $this->cart_cost;

        if (isset($this->discount) && !is_null($this->discount)) {
            $promo_code = Coupons::where('code', $this->discount)->first();
            $this->discount = $promo_code->id ?? 0;
            $this->sub_total -= (($promo_code->mount ??0 / 100) * $this->sub_total);
        }
        if (isset($this->data['cash_back']) && !is_null($this->data['cash_back'])) {
            // $this->user->update([
            //     'total_point' => $this->user->total_point - $this->data['cash_back'],
            // ]);
            $this->sub_total -= $this->data['cash_back'];
        }

        if (isset($this->data['card_id']) && isset($this->data['amount']) && !is_null($this->data['card_id']) && !is_null($this->data['amount'])) {
            $gift_wattle = GiftWallet::where('id', $this->data['card_id'])->first();
            $gift_wattle->update([
                'remaining' => $gift_wattle->remaining - $this->data['amount'],
            ]);
            $this->sub_total -= $this->data['amount'];
        }

        return $this;
    }

    public function handleOrderCreation()
    {
        $country = isset($this->data['country_id']) ? Country::find($this->data['country_id']) : 0;
        $city = isset($this->data['state_id']) ? City::find($this->data['state_id']) : 0;
//     $promoCode = PromoCode::where('code', $this->data['promo_code_id'])
//         ->where('status', 1)
//         ->where('end', '>=', today())
//         ->first();

// if($promoCode){
//     toastr()->success('promocode is done');
// }else{
//     toastr()->error('promocode is invalid');

// }
        $this->order = Order::create([
            'code_order' => $this->generateOrderNumber(),
            'client_id' => $this->user->id ?? $this->ip,
            'status' => Order::ORDER_STATUSES['منتظر التأكيد'],
            'payment_type' => Order::PAYMENT_TYPES['ONLINE'],
            'payment_status' => Order::PAYMENT_STATUSES['UNPAID'],
            'delivery_cost' => $this->delivery_cost,
            'sub_total' => $this->sub_total,
            'total' => $this->total + $this->delivery_cost-$this->cashback??0,
            'promo_code_id' => $this->discount ?? null,
            'amount' => $this->data['amount'] ?? null,
            'cash_back_amount' => $this->data['cash_back'] ?? null,

            'address' => $this->data['address'] ?? null,
            'user_name' => $this->data['name'] ?? null,
            'user_email' => $this->data['email'] ?? null,
            'user_phone' => $this->data['phone'] ?? null,
            'state_id' => $this->data['state_id'] ?? null,
            'zip_code' => $this->data['zip_code'] ?? null,
            'neighborhood' => $this->data['neighborhood'] ?? null,
            'country_ref_code' => $country ? $country->country_ref_code : null,
            'state_name' => "hhh",
            // 'payment_ref_code',
        ]);
        return $this;
    }

    public function updateOrder()
    {
        $paytabs_response_code = Order::PAYTABS_RESPONSE_STATUSES[$this->data['response_status']];
        $country = isset($this->data['country_id']) ? Country::find($this->data['country_id']) : 0;
        $city = isset($this->data['state_id']) ? City::find($this->data['state_id']) : 0;

        $this->order->update([
            'payment_status' => $paytabs_response_code,
            'payment_ref_code' => $this->data['payment_ref_code'],
            'address' => isset($this->data['address']) ? $this->data['address'] : null,
            'user_name' => isset($this->data['user_name']) ? $this->data['user_name'] : null,
            'user_email' => isset($this->data['user_email']) ? $this->data['user_email'] : null,
            'user_phone' => isset($this->data['user_phone']) ? $this->data['user_phone'] : null,
            'state_id' => isset($this->data['state_id']) ? $this->data['state_id'] : null,
            'country_id' => isset($this->data['country_id']) ? $this->data['country_id'] : null,
            'zip_code' => isset($this->data['zip_code']) ? $this->data['zip_code'] : null,
            'country_ref_code' => $country ? $country->country_ref_code : null,
            'state_name' => $city ? $country->name : null,
        ]);

        $this->user->update([
            'total_point' => $this->user->total_point - $this->order->cash_back_amount,
        ]);

        return $this;
    }

    public function rateOrder()
    {
        $this->order->update([
            'rate_status' => $this->data['rate_status'],
            'rate_comment' => $this->data['rate_comment'],
        ]);
        return $this;
    }

    public function handleBonuses()
    {
        $bonuses = Bouns::get();
        foreach ($bonuses as $bonus) {
            if ($this->order->total >= $bonus->start_bouns && $this->order->total <= $bonus->end_bonus) {
                BounsPoint::create([
                    'bouns_id' => $bonus->id,
                    'order_id' => $this->order->id,
                    'client_id' => $this->user->id,
                    'order_point' => $bonus->point,
                ]);
                $this->user->update([
                    'total_point' => $this->user->total_point + $bonus->point,
                ]);
            }
        }
        return $this;
    }

    public function moveCartItemsToOrderItems()
    {
        $order = $this->order;
        $this->cart_items->map(function ($cart_item) use ($order) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cart_item->product_id,
                'quantity' => $cart_item->quantity,
            ]);
            $cart_item->delete();
        });

        return $this;
    }

    public function setOrder(Order $order): self
    {
        $order = $this->order;
        return $this;
    }


}
