<?php

namespace App\Exceptions;

use Exception;
use PHPUnit\Event\Code\Throwable;

class PaymentException extends Exception
{
    protected $orderId;
    protected $paymentMethod;

    public function __construct($message = "", $orderId = null, $paymentMethod = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->orderId = $orderId;
        $this->paymentMethod = $paymentMethod;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'order_id' => $this->orderId,
            'payment_method' => $this->paymentMethod
        ], 422);
    }
}
