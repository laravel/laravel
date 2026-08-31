<?php

namespace Illuminate\Validation\Rules;

use Illuminate\Support\Traits\Conditionable;
use Stringable;

class Dimensions implements Stringable
{
    use Conditionable;

    /**
     * The constraints for the dimensions rule.
     *
     * @var array
     */
    protected $constraints = [];

    /**
     * Create a new dimensions rule instance.
     *
     * @param  array  $constraints
     */
    public function __construct(array $constraints = [])
    {
        $this->constraints = $constraints;
    }

    /**
     * Set the "width" constraint.
     *
     * @param  int  $value
     * @return $this
     */
    public function width($value)
    {
        $this->constraints['width'] = $value;

        return $this;
    }

    /**
     * Set the "height" constraint.
     *
     * @param  int  $value
     * @return $this
     */
    public function height($value)
    {
        $this->constraints['height'] = $value;

        return $this;
    }

    /**
     * Set the "min width" constraint.
     *
     * @param  int  $value
     * @return $this
     */
    public function minWidth($value)
    {
        $this->constraints['min_width'] = $value;

        return $this;
    }

    /**
     * Set the "min height" constraint.
     *
     * @param  int  $value
     * @return $this
     */
    public function minHeight($value)
    {
        $this->constraints['min_height'] = $value;

        return $this;
    }

    /**
     * Set the "max width" constraint.
     *
     * @param  int  $value
     * @return $this
     */
    public function maxWidth($value)
    {
        $this->constraints['max_width'] = $value;

        return $this;
    }

    /**
     * Set the "max height" constraint.
     *
     * @param  int  $value
     * @return $this
     */
    public function maxHeight($value)
    {
        $this->constraints['max_height'] = $value;

        return $this;
    }

    /**
     * Set the "ratio" constraint.
     *
     * @param  float  $value
     * @return $this
     */
    public function ratio($value)
    {
        $this->constraints['ratio'] = $value;

        return $this;
    }

    /**
     * Set the minimum aspect ratio.
     *
     * @param  float  $value
     * @return $this
     */
    public function minRatio($value)
    {
        $this->constraints['min_ratio'] = $value;

        return $this;
    }

    /**
     * Set the maximum aspect ratio.
     *
     * @param  float  $value
     * @return $this
     */
    public function maxRatio($value)
    {
        $this->constraints['max_ratio'] = $value;

        return $this;
    }

    /**
     * Set the aspect ratio range.
     *
     * @param  float  $min
     * @param  float  $max
     * @return $this
     */
    public function ratioBetween($min, $max)
    {
        $this->constraints['min_ratio'] = $min;
        $this->constraints['max_ratio'] = $max;

        return $this;
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString()
    {
        $result = '';

        foreach ($this->constraints as $key => $value) {
            $result .= "$key=$value,";
        }

        return 'dimensions:'.substr($result, 0, -1);
    }
}
