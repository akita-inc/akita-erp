<?php

namespace App\Providers;

use App\CoreExtensions\SessionGuardExtended;
use App\Helpers\CustomValidation;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend(
            'sessionExtended',
            function ($app) {
                $provider = new EloquentUserProvider($app['hash'], config('auth.providers.users.model'));

                $guard = new SessionGuardExtended($app['config']['auth.defaults.guard'], $provider, $this->app['session.store']);

                // When using the remember me functionality of the authentication services we
                // will need to be set the encryption instance of the guard, which allows
                // secure, encrypted cookie values to get generated for those cookies.
                if (method_exists($guard, 'setCookieJar')) {
                    $guard->setCookieJar($this->app['cookie']);
                }

                if (method_exists($guard, 'setDispatcher')) {
                    $guard->setDispatcher($this->app['events']);
                }

                if (method_exists($guard, 'setRequest')) {
                    $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
                }

                return $guard;
            }
        );
        CustomValidation::validate2ByteString();
        CustomValidation::validate1ByteString();
        CustomValidation::validateNumberAndDashes();
        CustomValidation::validateCharacterEmail();
        CustomValidation::validateFormatEmail();
        CustomValidation::validateRequiredSelect();
        CustomValidation::validateNumber();
        CustomValidation::validateLength();
        CustomValidation::validatePhoneNumber();
        CustomValidation::validateFaxNumber();
        CustomValidation::validateZipCode();
        CustomValidation::validateKana();
        CustomValidation::validateBetween();
        CustomValidation::validateDateFormat();
        CustomValidation::validateFileSize();
        CustomValidation::validateDecimal();
        CustomValidation::validateNumberRange();
        CustomValidation::validateImage();
        CustomValidation::validateKanaCustom();
        CustomValidation::validateHiragana();
        CustomValidation::validateRequireLength();
        CustomValidation::validateNumberRangeCustom();
    }
}
