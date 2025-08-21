<?php

namespace App\Providers;

use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use App\Contracts\PaymentServiceContract;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Cookie\Middleware\EncryptCookies;
// use Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (
            isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) {
            URL::forceScheme('https');
        }

        // Disable lazy loading to avoid n+1 problem (except on production server)
        // Model::preventLazyLoading(!$this->app->isProduction());

        Blade::withoutDoubleEncoding();
        Paginator::useBootstrapThree();
        // Artisan::call('dump-autoload');

        // Add Google recaptcha validation rule
        Validator::extend('recaptcha', 'App\\Helpers\\ReCaptcha@validate');

        // Disable encryption for gdpr cookie
        $this->app->resolving(EncryptCookies::class, function (EncryptCookies $encryptCookies) {
            $encryptCookies->disableFor(config('gdpr.cookie.name'));
        });

        // Add pagination on collections
        if (!Collection::hasMacro('paginate')) {
            Collection::macro('paginate', function ($perPage = 15, $page = null, $options = []) {
                $q = url()->full();
                // Remove unwanted page parameter from the url if exist
                if (Request::has('page')) {
                    $q = remove_url_parameter($q, 'page');
                }

                $page = $page ?? Paginator::resolveCurrentPage() ?? 1;

                $paginator = new LengthAwarePaginator($this->forPage($page, $perPage), $this->count(), $perPage, $page, $options);

                return $paginator->withPath($q);
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Need for cashier
        //Cashier::ignoreMigrations();
        Cashier::useCustomerModel('App\\Models\\Shop');

        // Payment method binding for wallet deposit
        if (Request::has('payment_method')) {
            $className = $this->resolvePaymentDependency(Request::get('payment_method'));
            $this->app->bind(PaymentServiceContract::class, $className);
        }

        // On demand Image manipulation
        $this->app->singleton(
            \League\Glide\Server::class,
            function ($app) {
                $filesystem = $app->make(Filesystem::class);

                return \League\Glide\ServerFactory::create([
                    'response' => new \League\Glide\Responses\SymfonyResponseFactory(app('request')),
                    'driver' => config('image.driver'),
                    'presets' => config('image.sizes'),
                    'source' => $filesystem->getDriver(),
                    'cache' => $filesystem->getDriver(),
                    'cache_path_prefix' => config('image.cache_dir'),
                    'base_url' => 'image', //Don't change this value
                ]);
            }
        );
    }

    /**
     * Resolve the payment dependency based on the given class/paymentGateway name.
     *
     * @param string $class_name Payment gateway name.
     *
     * @return string Fully qualified class name.
     *
     * @throws \ErrorException
     */
    private function resolvePaymentDependency(string $class_name): string
    {
        // Mapping of payment gateways to their respective service classes
        $paymentServices = [
            'stripe' => [
                'default' => \App\Services\Payments\StripeWebPaymentService::class,
                'wallet' => \App\Services\Payments\StripePaymentService::class,
            ],
            'saved_card' => \App\Services\Payments\StripePaymentService::class,
            'instamojo' => \Incevio\Package\Instamojo\Services\InstamojoPaymentService::class,
            'authorizenet' => \Incevio\Package\AuthorizeNet\Services\AuthorizeNetPaymentService::class,
            'cybersource' => \App\Services\Payments\CybersourcePaymentService::class,
            'paystack' => \Incevio\Package\Paystack\Services\PaystackPaymentService::class,
            'paypal' => \App\Services\Payments\PaypalPaymentService::class,
            'iyzico' => \Incevio\Package\Iyzico\Services\IyzicoPaymentService::class,
            'paypal-marketplace' => \Incevio\Package\PaypalMarketplace\Services\PaypalMarketplacePaymentService::class,
            'wire' => \App\Services\Payments\WirePaymentService::class,
            'cod' => \App\Services\Payments\CodPaymentService::class,
            'pip' => \App\Services\Payments\PipPaymentService::class,
            'zcart-wallet' => \Incevio\Package\Wallet\Services\WalletPaymentService::class,
            'razorpay' => \Incevio\Package\Razorpay\Services\RazorpayPaymentService::class,
            'sslcommerz' => \Incevio\Package\SslCommerz\Services\SslCommerzPaymentService::class,
            'flutterwave' => \Incevio\Package\FlutterWave\Services\FlutterWavePaymentService::class,
            'mpesa' => \Incevio\Package\MPesa\Services\MPesaPaymentService::class,
            'payfast' => \Incevio\Package\Payfast\Services\PayfastPaymentService::class,
            'mercado-pago' => \Incevio\Package\MercadoPago\Services\MercadoPagoPaymentService::class,
            'orangemoney' => \Incevio\Package\OrangeMoney\Services\OrangeMoneyPaymentService::class,
            'mollie' => \Incevio\Package\Mollie\Services\MolliePaymentService::class,
            'bkash' => \Incevio\Package\Bkash\Services\BkashPaymentService::class,
            'paytm' => \Incevio\Package\Paytm\Services\PaytmPaymentService::class,
            'twoCheckout' => \Incevio\Package\twoCheckout\Services\twoCheckoutPaymentService::class,
            'upiPayment' => \Incevio\Package\UpiPayment\Services\UpiPaymentService::class,
            'mtnMoney' => \Incevio\Package\MtnMoney\Services\MtnMoneyPaymentService::class,
        ];

        // Special handling for Stripe to differentiate between wallet deposits
        if ($class_name === 'stripe') {
            return stripos(request()->path(), 'wallet/deposit') !== false
                ? $paymentServices['stripe']['wallet']
                : $paymentServices['stripe']['default'];
        }

        // Lookup the class name in the array
        if (isset($paymentServices[$class_name])) {
            return $paymentServices[$class_name];
        }

        // Throw an error if the payment method is not found
        throw new \ErrorException("Error: Payment Method {$class_name} Not Found.");
    }
}
