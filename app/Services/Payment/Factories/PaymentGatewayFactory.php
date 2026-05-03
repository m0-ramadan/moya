<?php

namespace App\Services\Payment\Factories;

use InvalidArgumentException;
use Illuminate\Support\Facades\App;
use App\Services\Wallet\UserWalletService;
use App\Services\Wallet\ExchangeRateService;
use App\Services\Payment\Gateways\TabbyGateway;
use App\Services\Payment\Gateways\PaymobGateway;
use App\Services\Payment\Gateways\TamaraGateway;
use App\Services\Payment\Gateways\WalletGateway;

class PaymentGatewayFactory
{
    protected UserWalletService $walletService;
    protected ExchangeRateService $exchangeRateService;

    public function __construct(
        UserWalletService $walletService,
        ExchangeRateService $exchangeRateService
    ) {
        $this->walletService = $walletService;
        $this->exchangeRateService = $exchangeRateService;
    }

    public function make(string $gateway)
    {
        return match ($gateway) {
            'paymob' => App::make(PaymobGateway::class),
            'tamara' => App::make(TamaraGateway::class),
            'tabby' => App::make(TabbyGateway::class),
          //  'wallet' => App::make(WalletGateway::class),
            default => throw new InvalidArgumentException("Gateway [{$gateway}] is not supported."),
        };
    }
public function getAvailableGateways(): array
{
    return [
        // 'paymob' => [
        //     'name' => 'Paymob',
        //     'description' => 'البطاقات الائتمانية ومدى',
        //     'methods' => ['credit_card', 'mada'],
        //     'icon' => 'credit-card',
        //     'image' => asset('/images/payments/paymob.png'), // الصورة هنا
        //     'supports_save_card' => true,
        // ],
        'tamara' => [
            'name' => 'تمارا',
            'description' => 'الدفع بالتقسيط',
            'methods' => ['installments'],
            'icon' => 'calendar',
            'image' => asset('/images/payments/tamara.jpeg'),
            'supports_installments' => true,
        ],
        // 'tabby' => [
        //     'name' => 'تابي',
        //     'description' => 'الدفع لاحقاً',
        //     'methods' => ['pay_later'],
        //     'icon' => 'clock',
        //     'image' => asset('/images/payments/tabby.webp'),
        //     'supports_pay_later' => true,
        // ],
        // 'wallet' => [
        //     'name' => 'المحفظة',
        //     'description' => 'الدفع من رصيدك',
        //     'methods' => ['wallet'],
        //     'icon' => 'wallet',
        //     'image' => asset('/images/payments/wallet.png'),
        //     'requires_balance' => true,
        // ],
        'cash_on_delivery' => [
            'name' => 'الدفع عند الاستلام',
            'description' => 'الدفع عند استلام الطلب',
            'methods' => ['cash_on_delivery'],
            'icon' => 'money-bill',
            'image' => asset('/images/payments/cod.jpg'),
            'requires_balance' => false,
        ],
    ];
}

}