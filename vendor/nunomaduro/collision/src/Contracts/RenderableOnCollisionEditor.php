<?php

namespace NunoMaduro\Collision\Contracts;

use Whoops\Exception\Frame;

interface RenderableOnCollisionEditor
{
    /**
     * Returns the frame to be used on the Collision Editor.
     */
    public function toCollisionEditor(): Frame;
}
