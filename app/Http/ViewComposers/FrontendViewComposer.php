<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class FrontendViewComposer
{
    /**
     * Binds fallback values for each key from views/frontend.php onto to the
     * view. Any values the view provided are kept for each key.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $existing = $view->getData();
        $frontend = require resource_path('views/frontend.php');

        foreach ($frontend as $key => $value) {
            $view->with($key, array_merge([], $value, array_get($existing, $key) ?? []));
        }
    }
}
