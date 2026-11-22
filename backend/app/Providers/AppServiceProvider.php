<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blueprint::macro('ulidColumn', function (string $name) {
            /** @var Blueprint $this */
            return $this->char($name, 26)->charset('ascii')->collation('ascii_bin');
        });

        // use in development throws exception in case of lazy loading and trying to fill a field thats not in fillable
        // or when trying to read an attribute that wasnt selected
        Model::shouldBeStrict(! app()->isProduction());
    }
}
