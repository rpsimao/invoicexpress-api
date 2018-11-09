<?php

namespace rpsimao\InvoiceXpressAPI;

use Illuminate\Support\ServiceProvider;
use rpsimao\InvoiceXpressAPI\Service\InvoiceXpressAPI;
use rpsimao\InvoiceXpressAPI\Models\InvoiceXpressapiClients;

class InvoiceXpressAPIServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
         $this->publishes([
            __DIR__.'/../config/invoicexpress.php' => $this->app->configPath().'/invoicexpress.php',
        ], 'ivxapi-config');

        if (! class_exists('CreateInvoiceXpressapiClientsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../database/migrations/create_invoice_xpressapi_clients_table.php.stub' => $this->app->databasePath()."/migrations/{$timestamp}_create_invoice_xpressapi_clients_table.php",
            ], 'ivxapi-migrations');
        }

	    if (! class_exists('AddInvoiceXpressapiUserRelationship')) {
		    $timestamp = date('Y_m_d_His', time());

		    $this->publishes([
			    __DIR__ . '/../database/migrations/add_invoice_xpressapi_user_relationship.php.stub' => $this->app->databasePath()."/migrations/{$timestamp}_add_invoice_xpressapi_user_relationship.php",
		    ], 'ivxapi-migrateauth');
	    }


    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
	    $this->mergeConfigFrom(
		    __DIR__.'/../config/invoicexpress.php',
		    'invoicexpress'
	    );

	    $this->app->singleton( InvoiceXpressAPI::class, function (){
		    return new InvoiceXpressAPI();
	    });
	    $this->app->alias(InvoiceXpressAPI::class, 'InvoiceXpressAPI');

	    $this->app->singleton( InvoiceXpressapiClients::class, function (){
		    return new InvoiceXpressapiClients();
	    });

	    $this->app->alias(InvoiceXpressapiClients::class, 'InvoiceXpressapiClients');

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return ['InvoiceXpressAPI', 'InvoiceXpressapiClients'];
    }

}
