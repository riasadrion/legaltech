<?php

namespace App\Providers;

use App\Domain\Payment\Drivers\CoinbaseCommerceDriver;
use App\Domain\Payment\Drivers\CoinGateDriver;
use App\Domain\Payment\Drivers\FlutterwaveDriver;
use App\Domain\Payment\Drivers\IzipayDriver;
use App\Domain\Payment\Drivers\ManualDriver;
use App\Domain\Payment\Drivers\MercadoPagoDriver;
use App\Domain\Payment\Drivers\MollieDriver;
use App\Domain\Payment\Drivers\NowPaymentsDriver;
use App\Domain\Payment\Drivers\PayPalDriver;
use App\Domain\Payment\Drivers\PaystackDriver;
use App\Domain\Payment\Drivers\RazorpayDriver;
use App\Domain\Payment\Drivers\StripeDriver;
use App\Domain\Payment\PaymentGatewayManager;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class, function () {
            $manager = new PaymentGatewayManager;
            $manager->register(new ManualDriver);
            $manager->register(new StripeDriver);
            $manager->register(new PayPalDriver);
            $manager->register(new RazorpayDriver);
            $manager->register(new PaystackDriver);
            $manager->register(new FlutterwaveDriver);
            $manager->register(new MollieDriver);
            $manager->register(new MercadoPagoDriver);
            $manager->register(new CoinbaseCommerceDriver);
            $manager->register(new CoinGateDriver);
            $manager->register(new NowPaymentsDriver);
            $manager->register(new IzipayDriver);

            return $manager;
        });
    }
}
