<?php

namespace rpsimao\InvoiceXpressAPI;

use Illuminate\Support\ServiceProvider;
use \Config as Config;

class InvoiceXpressAPIServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
         $this->publishes([
            __DIR__.'/../config/permission.php' => $this->app->configPath().'/invoicexpress-api.php  ',
        ], 'ivxapi-config');

        if (! class_exists('CreateInvoiceXpressClientsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_invoice_xpress_clients_table.php.stub' => $this->app->databasePath()."/migrations/{$timestamp}_create_invoice_xpress_clients_table.php",
            ], 'migrations');
        }

         $this->app->bind(InvoiceXpressAPIContract::class,  rpsimao\Models\InvoiceXpressAPIClients::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
        $this->mergeConfigFrom(
            __DIR__.'/../config/invoicexpress-api.php',
            'invoicexpress-api'
        );
        
         $this->app->singleton('InvoiceXpressAPI', function ($app) {

            return new Service\InvoiceXpressAPI();
        });

        $this->app->alias('InvoiceXpressAPI', 'Service\InvoiceXpressAPI');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['InvoiceXpressAPI'];
    }

}