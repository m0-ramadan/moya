<?php

namespace App\Services\Payment\Factories;

use App\Services\Payment\Gateways\PaymobGateway;
use App\Services\Payment\Gateways\TamaraGateway;
use App\Services\Payment\Gateways\TabbyGateway;
use App\Services\Payment\Gateways\WalletGateway;
use App\Services\Wallet\UserWalletService;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    protected UserWalletService $walletService;

    public function __construct(UserWalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function make(string $gateway)
    {
        return match ($gateway) {
            'paymob' => app(PaymobGateway::class),
            'tamara' => app(TamaraGateway::class),
            'tabby' => app(TabbyGateway::class),
            // 'wallet' => app(WalletGateway::class),
            default => throw new InvalidArgumentException("Gateway [{$gateway}] is not supported."),
        };
    }

    public function getAvailableGateways(): array
    {
        return [
            'paymob' => [
                'name' => 'Paymob',
                'description' => 'البطاقات الائتمانية ومدى',
                'methods' => ['credit_card', 'mada'],
                'icon' => 'credit-card',
                'supports_save_card' => true,
            ],
            'tamara' => [
                'name' => 'تمارا',
                'description' => 'الدفع بالتقسيط',
                'methods' => ['installments'],
                'icon' => 'calendar',
                'supports_installments' => true,
            ],
            'tabby' => [
                'name' => 'تابي',
                'description' => 'الدفع لاحقاً',
                'methods' => ['pay_later'],
                'icon' => 'clock',
                'supports_pay_later' => true,
            ],
            'wallet' => [
                'name' => 'المحفظة',
                'description' => 'الدفع من رصيدك',
                'methods' => ['wallet'],
                'icon' => 'wallet',
                'requires_balance' => true,
            ],
        ];
    }
}
