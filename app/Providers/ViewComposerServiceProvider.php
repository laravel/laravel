<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Http\ViewComposers\PageDefaultsViewComposer;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Here the 'app/' directory is assumed to be all the individual pages,
        // and does not contain partials, or layouts. This is because the
        // composer will be ran multiple times if the Blade template extends
        // from files also in the 'app/' directory.
        View::composer('app/*', PageDefaultsViewComposer::class);
    }
}
