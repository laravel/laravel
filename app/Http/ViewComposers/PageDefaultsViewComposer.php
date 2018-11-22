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
                'title' => 'Page Title',
                'site_name' => 'Site Name',
                'description' => 'Page description',
                'meta_description' => 'Meta description',
                'social_image' => '/assets/img/meta/share.png',
                'url' => 'http://www.example.com/',
                'creator_twitter_handle' => '@author_handle',
                'site_twitter_handle' => '@site_handle',
                'share_title' => 'Share title',
                'share_description' => 'Share description',
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
                'title' => 'Page Title',
                'site_name' => 'Site Name',
                'description' => 'Page description',
                'meta_description' => 'Meta description',
                'social_image' => '/assets/img/meta/share.png',
                'url' => 'http://www.example.com/',
                'creator_twitter_handle' => '@author_handle',
                'site_twitter_handle' => '@site_handle',
                'share_title' => 'Share title',
                'share_description' => 'Share description',
            ],
        ];
    }
}
