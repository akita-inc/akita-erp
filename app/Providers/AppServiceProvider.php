<?php

namespace App\Providers;

use App\Helpers\CustomValidation;
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
    }
}
