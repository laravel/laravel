<?php

namespace Intervention\Image;

class Font
{
    /**
     * Text to be written
     *
     * @var String
     */
    private $text;

    /**
     * Text size in pixels
     *
     * @var integer
     */
    private $size = 12;

    /**
     * Color of the text
     *
     * @var mixed
     */
    private $color = '000000';

    /**
     * Rotation angle of the text
     *
     * @var integer
     */
    private $angle = 0;

    /**
     * Horizontal alignment of the text
     *
     * @var String
     */
    private $align;

    /**
     * Vertical alignment of the text
     *
     * @var String
     */
    private $valign;

    /**
     * Path to TTF or GD library internal font file of the text
     *
     * @var mixed
     */
    private $file;

    /**
     * Create a new instance of Font
     *
     * @param Strinf $text Text to be written
     */
    public function __construct($text = null)
    {
        $this->text = $text;
    }

    /**
     * Set text to be written
     *
     * @param  String $text
     * @return void
     */
    public function text($text)
    {
        $this->text = $text;
    }

    /**
     * Get text to be written
     *
     * @return String
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set font size in pixels
     *
     * @param  integer $size
     * @return void
     */
    public function size($size)
    {
        $this->size = $size;
    }

    /**
     * Get font size in pixels
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get font size in points
     *
     * @return integer
     */
    public function getPointSize()
    {
        return intval(ceil($this->size * 0.75));
    }

    /**
     * Set color of text to be written
     *
     * @param  mixed $color
     * @return void
     */
    public function color($color)
    {
        $this->color = $color;
    }

    /**
     * Get color of text
     *
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set rotation angle of text
     *
     * @param  integer $angle
     * @return void
     */
    public function angle($angle)
    {
        $this->angle = $angle;
    }

    /**
     * Get rotation angle of text
     *
     * @return integer
     */
    public function getAngle()
    {
        return $this->angle;
    }

    /**
     * Set horizontal text alignment
     *
     * @param  string $align
     * @return void
     */
    public function align($align)
    {
        $this->align = $align;
    }

    /**
     * Get horizontal text alignment
     *
     * @return string
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * Set vertical text alignment
     *
     * @param  string $valign
     * @return void
     */
    public function valign($valign)
    {
        $this->valign = $valign;
    }

    /**
     * Get vertical text alignment
     *
     * @return string
     */
    public function getValign()
    {
        return $this->valign;
    }

    /**
     * Set path to font file
     *
     * @param  string $align
     * @return void
     */
    public function file($file)
    {
        $this->file = $file;
    }

    /**
     * Get path to font file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Checks if current font has access to an applicable font file
     *
     * @return boolean [description]
     */
    private function hasApplicableFontFile()
    {
        if (is_string($this->file)) {
            return file_exists($this->file);
        }

        return false;
    }

    /**
     * Filter function to access internal integer font values
     *
     * @return integer
     */
    private function getInternalFont()
    {
        $internalfont = is_null($this->file) ? 1 : $this->file;
        $internalfont = is_numeric($internalfont) ? $internalfont : false;

        if ( ! in_array($internalfont, array(1, 2, 3, 4, 5))) {
            throw new Exception\FontNotFoundException(sprintf('Internal font %s not available.', $internalfont));
        }

        return intval($internalfont);
    }

    /**
     * Get width of an internal font character
     *
     * @return integer
     */
    private function getInternalFontWidth()
    {
        return $this->getInternalFont() + 4;
    }

    /**
     * Get height of an internal font character
     *
     * @return integer
     */
    private function getInternalFontHeight()
    {
        switch ($this->getInternalFont()) {
            case 1:
                return 8;

            case 2:
                return 14;

            case 3:
                return 14;

            case 4:
                return 16;

            case 5:
                return 16;
        }
    }

    /**
     * Calculates bounding box of current font setting
     *
     * @return Array
     */
    public function getBoxSize()
    {
        $box = array();

        if ($this->hasApplicableFontFile()) {

            // get bounding box with angle 0
            $box = imagettfbbox($this->getPointSize(), 0, $this->file, $this->text);

            // rotate points manually
            if ($this->angle != 0) {

                $angle = pi() * 2 - $this->angle * pi() * 2 / 360;

                for ($i=0; $i<4; $i++) {
                    $x = $box[$i * 2];
                    $y = $box[$i * 2 + 1];
                    $box[$i * 2] = cos($angle) * $x - sin($angle) * $y;
                    $box[$i * 2 + 1] = sin($angle) * $x + cos($angle) * $y;
                }
            }

            $box['width'] = intval(abs($box[4] - $box[0]));
            $box['height'] = intval(abs($box[5] - $box[1]));

        } else {

            // get current internal font size
            $width = $this->getInternalFontWidth();
            $height = $this->getInternalFontHeight();

            if (strlen($this->text) == 0) {
                // no text -> no boxsize
                $box['width'] = 0;
                $box['height'] = 0;
            } else {
                // calculate boxsize
                $box['width'] = strlen($this->text) * $width;
                $box['height'] = $height;
            }
        }

        return $box;
    }

    /**
     * Draws font to given image at given position
     * @param  Image   $image
     * @param  integer $posx
     * @param  integer $posy
     * @return void
     */
    public function applyToImage(Image $image, $posx = 0, $posy = 0)
    {
        // parse text color
        $color = $image->parseColor($this->color);

        if ($this->hasApplicableFontFile()) {

            if ($this->angle != 0 || is_string($this->align) || is_string($this->valign)) {

                $box = $this->getBoxSize();

                $align = is_null($this->align) ? 'left' : strtolower($this->align);
                $valign = is_null($this->valign) ? 'bottom' : strtolower($this->valign);

                // correction on position depending on v/h alignment
                switch ($align.'-'.$valign) {

                    case 'center-top':
                        $posx = $posx - round(($box[6]+$box[4])/2);
                        $posy = $posy - round(($box[7]+$box[5])/2);
                        break;

                    case 'right-top':
                        $posx = $posx - $box[4];
                        $posy = $posy - $box[5];
                        break;

                    case 'left-top':
                        $posx = $posx - $box[6];
                        $posy = $posy - $box[7];
                        break;

                    case 'center-center':
                    case 'center-middle':
                        $posx = $posx - round(($box[0]+$box[4])/2);
                        $posy = $posy - round(($box[1]+$box[5])/2);
                        break;

                    case 'right-center':
                    case 'right-middle':
                        $posx = $posx - round(($box[2]+$box[4])/2);
                        $posy = $posy - round(($box[3]+$box[5])/2);
                        break;

                    case 'left-center':
                    case 'left-middle':
                        $posx = $posx - round(($box[0]+$box[6])/2);
                        $posy = $posy - round(($box[1]+$box[7])/2);
                        break;

                    case 'center-bottom':
                        $posx = $posx - round(($box[0]+$box[2])/2);
                        $posy = $posy - round(($box[1]+$box[3])/2);
                        break;

                    case 'right-bottom':
                        $posx = $posx - $box[2];
                        $posy = $posy - $box[3];
                        break;

                    case 'left-bottom':
                        $posx = $posx - $box[0];
                        $posy = $posy - $box[1];
                        break;
                }
            }

            // $image->rectangle(array(0,0,0,0.5), $posx+$box[6], $posy+$box[7], $posx+$box[2], $posy+$box[3]);

            // enable alphablending for imagettftext
            imagealphablending($image->resource, true);

            // draw ttf text
            imagettftext($image->resource, $this->getPointSize(), $this->angle, $posx, $posy, $color, $this->file, $this->text);

        } else {

            // get box size
            $box = $this->getBoxSize();
            $width = $box['width'];
            $height = $box['height'];

            // internal font specific position corrections
            if ($this->getInternalFont() == 1) {
                $top_correction = 1;
                $bottom_correction = 2;
            } elseif ($this->getInternalFont() == 3) {
                $top_correction = 2;
                $bottom_correction = 4;
            } else {
                $top_correction = 3;
                $bottom_correction = 4;
            }

            // x-position corrections for horizontal alignment
            switch (strtolower($this->align)) {
                case 'center':
                    $posx = ceil($posx - ($width / 2));
                    break;

                case 'right':
                    $posx = ceil($posx - $width) + 1;
                    break;
            }

            // y-position corrections for vertical alignment
            switch (strtolower($this->valign)) {
                case 'center':
                case 'middle':
                    $posy = ceil($posy - ($height / 2));
                    break;

                case 'top':
                    $posy = ceil($posy - $top_correction);
                    break;

                default:
                case 'bottom':
                    $posy = round($posy - $height + $bottom_correction);
                    break;
            }

            // draw text
            imagestring($image->resource, $this->getInternalFont(), $posx, $posy, $this->text, $color);
        }
    }
}
