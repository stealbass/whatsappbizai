<?php

namespace App\Http\Composers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;

class SiteSettingComposer
{
    public function register(): void
    {
        View::composer('*', function ($view) {
            $view->with('site', SiteSetting::instance());
        });
    }
}
