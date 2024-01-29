<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Validator::extend('max_dimensions', function ($attribute, $value, $parameters, $validator) {
            list($maxWidth, $maxHeight) = $parameters;
            
        
        if (!$value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                // Jika bukan instance UploadedFile, maka tidak valid.
                return false;
            }

            [$width, $height] = getimagesize($value->getPathname());

            return $width <= $maxWidth && $height <= $maxHeight;
        });
    }
}
