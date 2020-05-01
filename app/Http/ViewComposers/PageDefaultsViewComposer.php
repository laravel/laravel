<?php

namespace App\Http\ViewComposers;

use Engage\LaravelFrontend\PageDefaultsViewComposer as BaseViewComposer;

class PageDefaultsViewComposer extends BaseViewComposer
{
    /**
     * Returns developer-defined application default variables.
     *
     * @return array
     */
    protected function app(): array
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
                'js' => [
                    'sprite' => (string) mix('/compiled/img/sprite.svg'),
                ],
            ],
            'links' => [
                'home' => route('home.show'),
            ],
        ];
    }

    /**
     * Returns developer-defined frontend default variables.
     *
     * @return array
     */
    protected function templates(): array
    {
        return [
            'links' => [
                'home' => route('templates.show', 'home/index'),
            ],
        ];
    }
}
