<?php

namespace Illuminate\Validation\Rules;

class ImageFile extends File
{
    /**
     * Create a new image file rule instance.
     *
     * @param  bool  $allowSvg
     */
    public function __construct($allowSvg = false)
    {
        if ($allowSvg) {
            $this->rules('image:allow_svg');
        } else {
            $this->rules('image');
        }
    }

    /**
     * The dimension constraints for the uploaded file.
     *
     * @param  \Illuminate\Validation\Rules\Dimensions  $dimensions
     * @return $this
     */
    public function dimensions($dimensions)
    {
        $this->rules($dimensions);

        return $this;
    }
}
