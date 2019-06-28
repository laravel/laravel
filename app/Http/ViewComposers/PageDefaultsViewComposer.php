<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use EngageInteractive\LaravelFrontend\PageDefaultsViewComposer as BaseViewComposer;

class PageDefaultsViewComposer extends BaseViewComposer
{
    /**
     * Gets frontend default variables.
     *
     * @return array
     */
    protected function defaultsForFrontend()
    {
        return [
            'page' => [
                'title' => 'Frontend | Page Title',
                'description' => 'Page description',
                'site_name' => 'Site Name',
                'social_image' => asset('/static/img/meta/share.png'),
                'url' => url()->current(),
                'creator_twitter_handle' => '@author_handle',
                'site_twitter_handle' => '@site_handle',
                'share_title' => 'Share title',
                'share_description' => 'Share description',
                'type' => 'article',
            ],
            'links' => [
                'home' => route('frontend.show', 'home/home'),
			],
        ];
    }

    /**
     * Gets application default variables (i.e. ones used when not in the
     * frontend templates.)
     *
     * @return array
     */
    protected function defaultsForApp()
    {
        return [
            'page' => [
                'title' => trans('meta.default.title'),
                'description' => trans('meta.default.description'),
                'site_name' => 'Site Name',
                'social_image' => asset('/static/img/meta/share.png'),
                'url' => url()->current(),
                'creator_twitter_handle' => '@author_handle',
                'site_twitter_handle' => '@site_handle',
                'share_title' => trans('meta.default.share_title'),
                'share_description' => trans('meta.default.share_description'),
                'type' => 'article',
			],
			'links' => [
				'home' => route('home.show'),
            ],
        ];
    }
}
