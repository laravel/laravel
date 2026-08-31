<?php

namespace Illuminate\Validation\Concerns;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\Exception\MathException as BrickMathException;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\Extra\SpoofCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Egulias\EmailValidator\Validation\RFCValidation;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Exceptions\MathException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationData;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ValueError;

trait ValidatesAttributes
{
    /**
     * Validate that an attribute was "accepted".
     *
     * This validation rule implies the attribute is "required".
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateAccepted($attribute, $value)
    {
        $acceptable = ['yes', 'on', '1', 1, true, 'true'];

        return $this->validateRequired($attribute, $value) && in_array($value, $acceptable, true);
    }

    /**
     * Validate that an attribute was "accepted" when another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateAcceptedIf($attribute, $value, $parameters)
    {
        $acceptable = ['yes', 'on', '1', 1, true, 'true'];

        $this->requireParameterCount(2, $parameters, 'accepted_if');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (in_array($other, $values, is_bool($other) || is_null($other))) {
            return $this->validateRequired($attribute, $value) && in_array($value, $acceptable, true);
        }

        return true;
    }

    /**
     * Validate that an attribute was "declined".
     *
     * This validation rule implies the attribute is "required".
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateDeclined($attribute, $value)
    {
        $acceptable = ['no', 'off', '0', 0, false, 'false'];

        return $this->validateRequired($attribute, $value) && in_array($value, $acceptable, true);
    }

    /**
     * Validate that an attribute was "declined" when another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateDeclinedIf($attribute, $value, $parameters)
    {
        $acceptable = ['no', 'off', '0', 0, false, 'false'];

        $this->requireParameterCount(2, $parameters, 'declined_if');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (in_array($other, $values, is_bool($other) || is_null($other))) {
            return $this->validateRequired($attribute, $value) && in_array($value, $acceptable, true);
        }

        return true;
    }

    /**
     * Validate that an attribute is an active URL.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateActiveUrl($attribute, $value)
    {
        if (! is_string($value)) {
            return false;
        }

        if ($url = parse_url($value, PHP_URL_HOST)) {
            try {
                $records = $this->getDnsRecords($url.'.', DNS_A | DNS_AAAA);

                if (is_array($records) && count($records) > 0) {
                    return true;
                }
            } catch (Exception) {
                return false;
            }
        }

        return false;
    }

    /**
     * Get the DNS records for the given hostname.
     *
     * @param  string  $hostname
     * @param  int  $type
     * @return array|false
     */
    protected function getDnsRecords($hostname, $type)
    {
        return dns_get_record($hostname, $type);
    }

    /**
     * Validate that an attribute is 7 bit ASCII.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateAscii($attribute, $value)
    {
        return Str::isAscii($value);
    }

    /**
     * "Break" on first validation fail.
     *
     * Always returns true, just lets us put "bail" in rules.
     *
     * @return bool
     */
    public function validateBail()
    {
        return true;
    }

    /**
     * Validate the date is before a given date.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateBefore($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'before');

        return $this->compareDates($attribute, $value, $parameters, '<');
    }

    /**
     * Validate the date is before or equal a given date.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateBeforeOrEqual($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'before_or_equal');

        return $this->compareDates($attribute, $value, $parameters, '<=');
    }

    /**
     * Validate the date is after a given date.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateAfter($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'after');

        return $this->compareDates($attribute, $value, $parameters, '>');
    }

    /**
     * Validate the date is equal or after a given date.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateAfterOrEqual($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'after_or_equal');

        return $this->compareDates($attribute, $value, $parameters, '>=');
    }

    /**
     * Compare a given date against another using an operator.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @param  string  $operator
     * @return bool
     */
    protected function compareDates($attribute, $value, $parameters, $operator)
    {
        if (! is_string($value) && ! is_numeric($value) && ! $value instanceof DateTimeInterface) {
            return false;
        }

        if ($format = $this->getDateFormat($attribute)) {
            return $this->checkDateTimeOrder($format, $value, $parameters[0], $operator);
        }

        if (is_null($date = $this->getDateTimestamp($parameters[0]))) {
            $date = $this->getDateTimestamp($this->getValue($parameters[0]));
        }

        return $this->compare($this->getDateTimestamp($value), $date, $operator);
    }

    /**
     * Get the date format for an attribute if it has one.
     *
     * @param  string  $attribute
     * @return string|null
     */
    protected function getDateFormat($attribute)
    {
        if ($result = $this->getRule($attribute, 'DateFormat')) {
            return $result[1][0];
        }
    }

    /**
     * Get the date timestamp.
     *
     * @param  mixed  $value
     * @return int|null
     */
    protected function getDateTimestamp($value)
    {
        $date = is_null($value) ? null : $this->getDateTime($value);

        return $date?->getTimestamp();
    }

    /**
     * Given two date/time strings, check that one is after the other.
     *
     * @param  string  $format
     * @param  string  $first
     * @param  string  $second
     * @param  string  $operator
     * @return bool
     */
    protected function checkDateTimeOrder($format, $first, $second, $operator)
    {
        $firstDate = $this->getDateTimeWithOptionalFormat($format, $first);

        $format = $this->getDateFormat($second) ?: $format;

        if (! $secondDate = $this->getDateTimeWithOptionalFormat($format, $second)) {
            if (is_null($second = $this->getValue($second))) {
                return true;
            }

            $secondDate = $this->getDateTimeWithOptionalFormat($format, $second);
        }

        return ($firstDate && $secondDate) && $this->compare($firstDate, $secondDate, $operator);
    }

    /**
     * Get a DateTime instance from a string.
     *
     * @param  string  $format
     * @param  string  $value
     * @return \DateTime|null
     */
    protected function getDateTimeWithOptionalFormat($format, $value)
    {
        if ($date = DateTime::createFromFormat('!'.$format, $value)) {
            return $date;
        }

        return $this->getDateTime($value);
    }

    /**
     * Get a DateTime instance from a string with no format.
     *
     * @param  string  $value
     * @return \DateTime|null
     */
    protected function getDateTime($value)
    {
        try {
            return @Date::parse($value) ?: null;
        } catch (Exception) {
            //
        }
    }

    /**
     * Validate that an attribute contains only alphabetic characters.
     * If the 'ascii' option is passed, validate that an attribute contains only ascii alphabetic characters.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateAlpha($attribute, $value, $parameters)
    {
        if (isset($parameters[0]) && $parameters[0] === 'ascii') {
            return is_string($value) && preg_match('/\A[a-zA-Z]+\z/u', $value);
        }

        return is_string($value) && preg_match('/\A[\pL\pM]+\z/u', $value);
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters, dashes, and underscores.
     * If the 'ascii' option is passed, validate that an attribute contains only ascii alpha-numeric characters,
     * dashes, and underscores.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateAlphaDash($attribute, $value, $parameters)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        if (isset($parameters[0]) && $parameters[0] === 'ascii') {
            return preg_match('/\A[a-zA-Z0-9_-]+\z/u', $value) > 0;
        }

        return preg_match('/\A[\pL\pM\pN_-]+\z/u', $value) > 0;
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters.
     * If the 'ascii' option is passed, validate that an attribute contains only ascii alpha-numeric characters.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateAlphaNum($attribute, $value, $parameters)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        if (isset($parameters[0]) && $parameters[0] === 'ascii') {
            return preg_match('/\A[a-zA-Z0-9]+\z/u', $value) > 0;
        }

        return preg_match('/\A[\pL\pM\pN]+\z/u', $value) > 0;
    }

    /**
     * Validate that an attribute is an array.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateArray($attribute, $value, $parameters = [])
    {
        if (! is_array($value)) {
            return false;
        }

        if (empty($parameters)) {
            return true;
        }

        return empty(array_diff_key($value, array_fill_keys($parameters, '')));
    }

    /**
     * Validate that an attribute is a list.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateList($attribute, $value)
    {
        return is_array($value) && array_is_list($value);
    }

    /**
     * Validate that an array has all of the given keys.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateRequiredArrayKeys($attribute, $value, $parameters)
    {
        if (! is_array($value)) {
            return false;
        }

        foreach ($parameters as $param) {
            if (! Arr::exists($value, $param)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate the size of an attribute is between a set of values.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateBetween($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'between');

        try {
            $size = BigNumber::of($this->getSize($attribute, $value));

            return $size->isGreaterThanOrEqualTo($this->trim($parameters[0])) && $size->isLessThanOrEqualTo($this->trim($parameters[1]));
        } catch (MathException) {
            return false;
        }
    }

    /**
     * Validate that an attribute is a boolean.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array{0: 'strict'}  $parameters
     * @return bool
     */
    public function validateBoolean($attribute, $value, $parameters)
    {
        $acceptable = [true, false, 0, 1, '0', '1'];

        if (($parameters[0] ?? null) === 'strict') {
            $acceptable = [true, false];
        }

        return in_array($value, $acceptable, true);
    }

    /**
     * Validate that an attribute has a matching confirmation.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array{0: string}  $parameters
     * @return bool
     */
    public function validateConfirmed($attribute, $value, $parameters)
    {
        return $this->validateSame($attribute, $value, [$parameters[0] ?? $attribute.'_confirmation']);
    }

    /**
     * Validate an attribute contains a list of values.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateContains($attribute, $value, $parameters)
    {
        if (! is_array($value)) {
            return false;
        }

        foreach ($parameters as $parameter) {
            if (! in_array($parameter, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate an attribute does not contain a list of values.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDoesntContain($attribute, $value, $parameters)
    {
        if (! is_array($value)) {
            return false;
        }

        foreach ($parameters as $parameter) {
            if (in_array($parameter, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate that the password of the currently authenticated user matches the given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    protected function validateCurrentPassword($attribute, $value, $parameters)
    {
        $auth = $this->container->make('auth');
        $hasher = $this->container->make('hash');

        $guard = $auth->guard(Arr::first($parameters));

        if ($guard->guest()) {
            return false;
        }

        return $hasher->check($value, $guard->user()->getAuthPassword());
    }

    /**
     * Validate that an attribute is a valid date.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateDate($attribute, $value)
    {
        if ($value instanceof DateTimeInterface) {
            return true;
        }

        try {
            if ((! is_string($value) && ! is_numeric($value)) || strtotime($value) === false) {
                return false;
            }
        } catch (Exception) {
            return false;
        }

        $date = date_parse($value);

        return checkdate($date['month'], $date['day'], $date['year']);
    }

    /**
     * Validate that an attribute matches a date format.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDateFormat($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'date_format');

        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        foreach ($parameters as $format) {
            try {
                $date = DateTime::createFromFormat('!'.$format, $value, new DateTimeZone('UTC'));

                if ($date && $date->format($format) == $value) {
                    return true;
                }
            } catch (ValueError) {
                return false;
            }
        }

        return false;
    }

    /**
     * Validate that an attribute is equal to another date.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDateEquals($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'date_equals');

        return $this->compareDates($attribute, $value, $parameters, '=');
    }

    /**
     * Validate that an attribute has a given number of decimal places.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDecimal($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'decimal');

        if (! $this->validateNumeric($attribute, $value, [])) {
            return false;
        }

        $matches = [];

        if (preg_match('/^[+-]?\d*\.?(\d*)$/', $value, $matches) !== 1) {
            return false;
        }

        $decimals = strlen(end($matches));

        if (! isset($parameters[1])) {
            return $decimals == $parameters[0];
        }

        return $decimals >= $parameters[0] &&
               $decimals <= $parameters[1];
    }

    /**
     * Validate that an attribute is different from another attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDifferent($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'different');

        foreach ($parameters as $parameter) {
            if (Arr::has($this->data, $parameter)) {
                $other = Arr::get($this->data, $parameter);

                if ($value === $other) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validate that an attribute has a given number of digits.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDigits($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'digits');

        return (is_numeric($value) || is_string($value)) &&
            ! preg_match('/[^0-9]/', $value) &&
            strlen((string) $value) == $parameters[0];
    }

    /**
     * Validate that an attribute is between a given number of digits.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDigitsBetween($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'digits_between');

        $length = strlen((string) $value);

        return ! preg_match('/[^0-9]/', $value)
                    && $length >= $parameters[0] && $length <= $parameters[1];
    }

    /**
     * Validate the dimensions of an image matches the given values.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDimensions($attribute, $value, $parameters)
    {
        if ($this->isValidFileInstance($value) && in_array($value->getMimeType(), ['image/svg+xml', 'image/svg'])) {
            return true;
        }

        if (! $this->isValidFileInstance($value)) {
            return false;
        }

        $dimensions = method_exists($value, 'dimensions')
            ? $value->dimensions()
            : @getimagesize($value->getRealPath());

        if (! $dimensions) {
            return false;
        }

        $this->requireParameterCount(1, $parameters, 'dimensions');

        [$width, $height] = $dimensions;

        $parameters = $this->parseNamedParameters($parameters);

        return ! (
            $this->failsBasicDimensionChecks($parameters, $width, $height) ||
            $this->failsRatioCheck($parameters, $width, $height) ||
            $this->failsMinRatioCheck($parameters, $width, $height) ||
            $this->failsMaxRatioCheck($parameters, $width, $height)
        );
    }

    /**
     * Test if the given width and height fail any conditions.
     *
     * @param  array<string,string>  $parameters
     * @param  int  $width
     * @param  int  $height
     * @return bool
     */
    protected function failsBasicDimensionChecks($parameters, $width, $height)
    {
        return (isset($parameters['width']) && $parameters['width'] != $width) ||
               (isset($parameters['min_width']) && $parameters['min_width'] > $width) ||
               (isset($parameters['max_width']) && $parameters['max_width'] < $width) ||
               (isset($parameters['height']) && $parameters['height'] != $height) ||
               (isset($parameters['min_height']) && $parameters['min_height'] > $height) ||
               (isset($parameters['max_height']) && $parameters['max_height'] < $height);
    }

    /**
     * Determine if the given parameters fail a dimension ratio check.
     *
     * @param  array<string,string>  $parameters
     * @param  int  $width
     * @param  int  $height
     * @return bool
     */
    protected function failsRatioCheck($parameters, $width, $height)
    {
        if (! isset($parameters['ratio'])) {
            return false;
        }

        [$numerator, $denominator] = array_replace(
            [1, 1], array_filter(sscanf($parameters['ratio'], '%f/%d'))
        );

        $precision = 1 / (max(($width + $height) / 2, $height) + 1);

        return abs($numerator / $denominator - $width / $height) > $precision;
    }

    /**
     * Determine if the given parameters fail a dimension minimum ratio check.
     *
     * @param  array<string,string>  $parameters
     * @param  int  $width
     * @param  int  $height
     * @return bool
     */
    private function failsMinRatioCheck($parameters, $width, $height)
    {
        if (! isset($parameters['min_ratio'])) {
            return false;
        }

        [$minNumerator, $minDenominator] = array_replace(
            [1, 1], array_filter(sscanf($parameters['min_ratio'], '%f/%d'))
        );

        return ($width / $height) > ($minNumerator / $minDenominator);
    }

    /**
     * Determine if the given parameters fail a dimension maximum ratio check.
     *
     * @param  array<string,string>  $parameters
     * @param  int  $width
     * @param  int  $height
     * @return bool
     */
    private function failsMaxRatioCheck($parameters, $width, $height)
    {
        if (! isset($parameters['max_ratio'])) {
            return false;
        }

        [$maxNumerator, $maxDenominator] = array_replace(
            [1, 1], array_filter(sscanf($parameters['max_ratio'], '%f/%d'))
        );

        return ($width / $height) < ($maxNumerator / $maxDenominator);
    }

    /**
     * Validate an attribute is unique among other values.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDistinct($attribute, $value, $parameters)
    {
        $data = Arr::except($this->getDistinctValues($attribute), $attribute);

        if (in_array('ignore_case', $parameters)) {
            return empty(preg_grep('/^'.preg_quote($value, '/').'$/iu', $data));
        }

        return ! in_array($value, array_values($data), in_array('strict', $parameters));
    }

    /**
     * Get the values to distinct between.
     *
     * @param  string  $attribute
     * @return array
     */
    protected function getDistinctValues($attribute)
    {
        $attributeName = $this->getPrimaryAttribute($attribute);

        if (! property_exists($this, 'distinctValues')) {
            return $this->extractDistinctValues($attributeName);
        }

        if (! array_key_exists($attributeName, $this->distinctValues)) {
            $this->distinctValues[$attributeName] = $this->extractDistinctValues($attributeName);
        }

        return $this->distinctValues[$attributeName];
    }

    /**
     * Extract the distinct values from the data.
     *
     * @param  string  $attribute
     * @return array
     */
    protected function extractDistinctValues($attribute)
    {
        $attributeData = ValidationData::extractDataFromPath(
            ValidationData::getLeadingExplicitAttributePath($attribute), $this->data
        );

        $pattern = str_replace('\*', '[^.]+', preg_quote($attribute, '#'));

        return Arr::where(Arr::dot($attributeData), function ($value, $key) use ($pattern) {
            return (bool) preg_match('#^'.$pattern.'\z#u', $key);
        });
    }

    /**
     * Validate that an attribute is a valid e-mail address.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateEmail($attribute, $value, $parameters)
    {
        if (! is_string($value) && ! (is_object($value) && method_exists($value, '__toString'))) {
            return false;
        }

        if (preg_match('/[\r\n]/', (string) $value) > 0) {
            return false;
        }

        $validations = (new Collection($parameters))
            ->unique()
            ->map(fn ($validation) => match (true) {
                $validation === 'strict' => new NoRFCWarningsValidation(),
                $validation === 'dns' => new DNSCheckValidation(),
                $validation === 'spoof' => new SpoofCheckValidation(),
                $validation === 'filter' => new FilterEmailValidation(),
                $validation === 'filter_unicode' => FilterEmailValidation::unicode(),
                is_string($validation) && class_exists($validation) => $this->container->make($validation),
                default => new RFCValidation(),
            })
            ->values()
            ->all() ?: [new RFCValidation];

        $emailValidator = Container::getInstance()->make(EmailValidator::class);

        return $emailValidator->isValid($value, new MultipleValidationWithAnd($validations));
    }

    /**
     * Validate the encoding of an attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateEncoding($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'encoding');

        if (! in_array(mb_strtolower($parameters[0]), array_map(mb_strtolower(...), mb_list_encodings()))) {
            throw new InvalidArgumentException("Validation rule encoding parameter [{$parameters[0]}] is not a valid encoding.");
        }

        return mb_check_encoding($value instanceof File ? $value->getContent() : $value, $parameters[0]);
    }

    /**
     * Validate the existence of an attribute value in a database table.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateExists($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'exists');

        [$connection, $table] = $this->parseTable($parameters[0]);

        // The second parameter position holds the name of the column that should be
        // verified as existing. If this parameter is not specified we will guess
        // that the columns being "verified" shares the given attribute's name.
        $column = $this->getQueryColumn($parameters, $attribute);

        $expected = is_array($value) ? count(array_unique($value)) : 1;

        if ($expected === 0) {
            return true;
        }

        return $this->getExistCount(
            $connection, $table, $column, $value, $parameters
        ) >= $expected;
    }

    /**
     * Get the number of records that exist in storage.
     *
     * @param  mixed  $connection
     * @param  string  $table
     * @param  string  $column
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return int
     */
    protected function getExistCount($connection, $table, $column, $value, $parameters)
    {
        $verifier = $this->getPresenceVerifier($connection);

        $extra = $this->getExtraConditions(
            array_values(array_slice($parameters, 2))
        );

        if ($this->currentRule instanceof Exists) {
            $extra = array_merge($extra, $this->currentRule->queryCallbacks());
        }

        return is_array($value)
            ? $verifier->getMultiCount($table, $column, $value, $extra)
            : $verifier->getCount($table, $column, $value, null, null, $extra);
    }

    /**
     * Validate the uniqueness of an attribute value on a given database table.
     *
     * If a database column is not specified, the attribute will be used.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateUnique($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'unique');

        [$connection, $table, $idColumn] = $this->parseTable($parameters[0]);

        // The second parameter position holds the name of the column that needs to
        // be verified as unique. If this parameter isn't specified we will just
        // assume that this column to be verified shares the attribute's name.
        $column = $this->getQueryColumn($parameters, $attribute);

        $id = null;

        if (isset($parameters[2])) {
            [$idColumn, $id] = $this->getUniqueIds($idColumn, $parameters);

            if (! is_null($id)) {
                $id = stripslashes($id);
            }
        }

        // The presence verifier is responsible for counting rows within this store
        // mechanism which might be a relational database or any other permanent
        // data store like Redis, etc. We will use it to determine uniqueness.
        $verifier = $this->getPresenceVerifier($connection);

        $extra = $this->getUniqueExtra($parameters);

        if ($this->currentRule instanceof Unique) {
            $extra = array_merge($extra, $this->currentRule->queryCallbacks());
        }

        return $verifier->getCount(
            $table, $column, $value, $id, $idColumn, $extra
        ) == 0;
    }

    /**
     * Get the excluded ID column and value for the unique rule.
     *
     * @param  string|null  $idColumn
     * @param  array<int, int|string>  $parameters
     * @return array
     */
    protected function getUniqueIds($idColumn, $parameters)
    {
        $idColumn ??= $parameters[3] ?? 'id';

        return [$idColumn, $this->prepareUniqueId($parameters[2])];
    }

    /**
     * Prepare the given ID for querying.
     *
     * @param  mixed  $id
     * @return int
     */
    protected function prepareUniqueId($id)
    {
        if (preg_match('/\[(.*)\]/', $id, $matches)) {
            $id = $this->getValue($matches[1]);
        }

        if (strtolower($id) === 'null') {
            $id = null;
        }

        if (filter_var($id, FILTER_VALIDATE_INT) !== false) {
            $id = (int) $id;
        }

        return $id;
    }

    /**
     * Get the extra conditions for a unique rule.
     *
     * @param  array<int, int|string>  $parameters
     * @return array
     */
    protected function getUniqueExtra($parameters)
    {
        if (isset($parameters[4])) {
            return $this->getExtraConditions(array_slice($parameters, 4));
        }

        return [];
    }

    /**
     * Parse the connection / table for the unique / exists rules.
     *
     * @param  string  $table
     * @return array
     */
    public function parseTable($table)
    {
        [$connection, $table] = str_contains($table, '.') ? explode('.', $table, 2) : [null, $table];

        if (str_contains($table, '\\') && class_exists($table) && is_a($table, Model::class, true)) {
            $model = new $table;

            $table = $model->getTable();
            $connection ??= $model->getConnectionName();

            if (str_contains($table, '.') && Str::startsWith($table, $connection)) {
                $connection = null;
            }

            $idColumn = $model->getKeyName();
        }

        return [$connection, $table, $idColumn ?? null];
    }

    /**
     * Get the column name for an exists / unique query.
     *
     * @param  array<int, int|string>  $parameters
     * @param  string  $attribute
     * @return int|string
     */
    public function getQueryColumn($parameters, $attribute)
    {
        return isset($parameters[1]) && $parameters[1] !== 'NULL'
            ? $parameters[1]
            : $this->guessColumnForQuery($attribute);
    }

    /**
     * Guess the database column from the given attribute name.
     *
     * @param  string  $attribute
     * @return string
     */
    public function guessColumnForQuery($attribute)
    {
        if (in_array($attribute, Arr::collapse($this->implicitAttributes))
                && ! is_numeric($last = last(explode('.', $attribute)))) {
            return $last;
        }

        return $attribute;
    }

    /**
     * Get the extra conditions for a unique / exists rule.
     *
     * @param  array  $segments
     * @return array
     */
    protected function getExtraConditions(array $segments)
    {
        $extra = [];

        $count = count($segments);

        for ($i = 0; $i < $count; $i += 2) {
            $extra[$segments[$i]] = $segments[$i + 1];
        }

        return $extra;
    }

    /**
     * Validate the extension of a file upload attribute is in a set of defined extensions.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateExtensions($attribute, $value, $parameters)
    {
        if (! $this->isValidFileInstance($value)) {
            return false;
        }

        if ($this->shouldBlockPhpUpload($value, $parameters)) {
            return false;
        }

        return in_array(strtolower($value->getClientOriginalExtension()), $parameters);
    }

    /**
     * Validate the given value is a valid file.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateFile($attribute, $value)
    {
        return $this->isValidFileInstance($value);
    }

    /**
     * Validate the given attribute is filled if it is present.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateFilled($attribute, $value)
    {
        if (Arr::has($this->data, $attribute)) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute is greater than another attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateGt($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'gt');

        $comparedToValue = $this->getValue($parameters[0]);

        $this->shouldBeNumeric($attribute, 'Gt');

        if (is_null($comparedToValue) && (is_numeric($value) && is_numeric($parameters[0]))) {
            try {
                return BigNumber::of($this->getSize($attribute, $value))->isGreaterThan($this->trim($parameters[0]));
            } catch (MathException) {
                return false;
            }
        }

        if (is_numeric($parameters[0])) {
            return false;
        }

        if ($this->hasRule($attribute, $this->numericRules) && is_numeric($value) && is_numeric($comparedToValue)) {
            try {
                return BigNumber::of($this->trim($value))->isGreaterThan($this->trim($comparedToValue));
            } catch (MathException) {
                return false;
            }
        }

        if (! $this->isSameType($value, $comparedToValue)) {
            return false;
        }

        try {
            return $this->getSize($attribute, $value) > $this->getSize($attribute, $comparedToValue);
        } catch (MathException) {
            return false;
        }
    }

    /**
     * Validate that an attribute is less than another attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateLt($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'lt');

        $comparedToValue = $this->getValue($parameters[0]);

        $this->shouldBeNumeric($attribute, 'Lt');

        if (is_null($comparedToValue) && (is_numeric($value) && is_numeric($parameters[0]))) {
            try {
                return BigNumber::of($this->getSize($attribute, $value))->isLessThan($this->trim($parameters[0]));
            } catch (MathException) {
                return false;
            }
        }

        if (is_numeric($parameters[0])) {
            return false;
        }

        if ($this->hasRule($attribute, $this->numericRules) && is_numeric($value) && is_numeric($comparedToValue)) {
            return BigNumber::of($this->trim($value))->isLessThan($this->trim($comparedToValue));
        }

        if (! $this->isSameType($value, $comparedToValue)) {
            return false;
        }

        try {
            return $this->getSize($attribute, $value) < $this->getSize($attribute, $comparedToValue);
        } catch (MathException) {
            return false;
        }
    }

    /**
     * Validate that an attribute is greater than or equal another attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateGte($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'gte');

        $comparedToValue = $this->getValue($parameters[0]);

        $this->shouldBeNumeric($attribute, 'Gte');

        if (is_null($comparedToValue) && (is_numeric($value) && is_numeric($parameters[0]))) {
            try {
                return BigNumber::of($this->getSize($attribute, $value))->isGreaterThanOrEqualTo($this->trim($parameters[0]));
            } catch (MathException) {
                return false;
            }
        }

        if (is_numeric($parameters[0])) {
            return false;
        }

        if ($this->hasRule($attribute, $this->numericRules) && is_numeric($value) && is_numeric($comparedToValue)) {
            try {
                return BigNumber::of($this->trim($value))->isGreaterThanOrEqualTo($this->trim($comparedToValue));
            } catch (MathException) {
                return false;
            }
        }

        if (! $this->isSameType($value, $comparedToValue)) {
            return false;
        }

        try {
            return $this->getSize($attribute, $value) >= $this->getSize($attribute, $comparedToValue);
        } catch (MathException) {
            return false;
        }
    }

    /**
     * Validate that an attribute is less than or equal another attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateLte($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'lte');

        $comparedToValue = $this->getValue($parameters[0]);

        $this->shouldBeNumeric($attribute, 'Lte');

        if (is_null($comparedToValue) && (is_numeric($value) && is_numeric($parameters[0]))) {
            try {
                return BigNumber::of($this->getSize($attribute, $value))->isLessThanOrEqualTo($this->trim($parameters[0]));
            } catch (MathException) {
                return false;
            }
        }

        if (is_numeric($parameters[0])) {
            return false;
        }

        if ($this->hasRule($attribute, $this->numericRules) && is_numeric($value) && is_numeric($comparedToValue)) {
            return BigNumber::of($this->trim($value))->isLessThanOrEqualTo($this->trim($comparedToValue));
        }

        if (! $this->isSameType($value, $comparedToValue)) {
            return false;
        }

        try {
            return $this->getSize($attribute, $value) <= $this->getSize($attribute, $comparedToValue);
        } catch (MathException) {
            return false;
        }
    }

    /**
     * Validate that an attribute is lowercase.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateLowercase($attribute, $value, $parameters)
    {
        return Str::lower($value) === $value;
    }

    /**
     * Validate that an attribute is uppercase.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateUppercase($attribute, $value, $parameters)
    {
        return Str::upper($value) === $value;
    }

    /**
     * Validate that an attribute is a valid HEX color.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateHexColor($attribute, $value)
    {
        return preg_match('/^#(?:(?:[0-9a-f]{3}){1,2}|(?:[0-9a-f]{4}){1,2})$/i', $value) === 1;
    }

    /**
     * Validate the MIME type of a file is an image MIME type.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, string>  $parameters
     * @return bool
     */
    public function validateImage($attribute, $value, $parameters = [])
    {
        $mimes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

        if (is_array($parameters) && in_array('allow_svg', $parameters)) {
            $mimes[] = 'svg';
        }

        return $this->validateMimes($attribute, $value, $mimes);
    }

    /**
     * Validate an attribute is contained within a list of values.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateIn($attribute, $value, $parameters)
    {
        if (is_array($value) && $this->hasRule($attribute, 'Array')) {
            foreach ($value as $element) {
                if (is_array($element)) {
                    return false;
                }
            }

            return count(array_diff($value, $parameters)) === 0;
        }

        return ! is_array($value) && in_array((string) $value, $parameters);
    }

    /**
     * Validate that the values of an attribute are in another attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateInArray($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'in_array');

        $explicitPath = ValidationData::getLeadingExplicitAttributePath($parameters[0]);

        $attributeData = ValidationData::extractDataFromPath($explicitPath, $this->data);

        $otherValues = Arr::where(Arr::dot($attributeData), function ($value, $key) use ($parameters) {
            return Str::is($parameters[0], $key);
        });

        return in_array($value, $otherValues);
    }

    /**
     * Validate that an array has at least one of the given keys.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateInArrayKeys($attribute, $value, $parameters)
    {
        if (! is_array($value)) {
            return false;
        }

        if (empty($parameters)) {
            return false;
        }

        foreach ($parameters as $param) {
            if (Arr::exists($value, $param)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate that an attribute is an integer.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array{0?: 'strict'}  $parameters
     * @return bool
     */
    public function validateInteger($attribute, $value, array $parameters = [])
    {
        if (($parameters[0] ?? null) === 'strict') {
            return is_int($value);
        }

        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate that an attribute is a valid IP.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateIp($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate that an attribute is a valid IPv4.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateIpv4($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Validate that an attribute is a valid IPv6.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateIpv6($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Validate that an attribute is a valid MAC address.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateMacAddress($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_MAC) !== false;
    }

    /**
     * Validate the attribute is a valid JSON string.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateJson($attribute, $value)
    {
        if (is_array($value) || is_null($value)) {
            return false;
        }

        if (! is_scalar($value) && ! method_exists($value, '__toString')) {
            return false;
        }

        return json_validate($value);
    }

    /**
     * Validate the size of an attribute is less than or equal to a maximum value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMax($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'max');

        if ($value instanceof UploadedFile && ! $value->isValid()) {
            return false;
        }

        try {
            return BigNumber::of($this->getSize($attribute, $value))->isLessThanOrEqualTo($this->trim($parameters[0]));
        } catch (MathException) {
            return false;
        }
    }

    /**
     * Validate that an attribute has a maximum number of digits.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMaxDigits($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'max_digits');

        $length = strlen((string) $value);

        return ! preg_match('/[^0-9]/', $value) && $length <= $parameters[0];
    }

    /**
     * Validate the guessed extension of a file upload is in a set of file extensions.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMimes($attribute, $value, $parameters)
    {
        if (! $this->isValidFileInstance($value)) {
            return false;
        }

        if ($this->shouldBlockPhpUpload($value, $parameters)) {
            return false;
        }

        if (in_array('jpg', $parameters) || in_array('jpeg', $parameters)) {
            $parameters = array_unique(array_merge($parameters, ['jpg', 'jpeg']));
        }

        return $value->getPath() !== '' && in_array($value->guessExtension(), $parameters);
    }

    /**
     * Validate the MIME type of a file upload attribute is in a set of MIME types.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMimetypes($attribute, $value, $parameters)
    {
        if (! $this->isValidFileInstance($value)) {
            return false;
        }

        if ($this->shouldBlockPhpUpload($value, $parameters)) {
            return false;
        }

        return $value->getPath() !== '' &&
                (in_array($value->getMimeType(), $parameters) ||
                 in_array(explode('/', $value->getMimeType())[0].'/*', $parameters));
    }

    /**
     * Check if PHP uploads are explicitly allowed.
     *
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    protected function shouldBlockPhpUpload($value, $parameters)
    {
        if (in_array('php', $parameters)) {
            return false;
        }

        $phpExtensions = [
            'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        ];

        return ($value instanceof UploadedFile)
            ? in_array(trim(strtolower($value->getClientOriginalExtension())), $phpExtensions)
            : in_array(trim(strtolower($value->getExtension())), $phpExtensions);
    }

    /**
     * Validate the size of an attribute is greater than or equal to a minimum value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMin($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'min');

        try {
            return BigNumber::of($this->getSize($attribute, $value))->isGreaterThanOrEqualTo($this->trim($parameters[0]));
        } catch (MathException) {
            return false;
        }
    }

    /**
     * Validate that an attribute has a minimum number of digits.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMinDigits($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'min_digits');

        $length = strlen((string) $value);

        return ! preg_match('/[^0-9]/', $value) && $length >= $parameters[0];
    }

    /**
     * Validate that an attribute is missing.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMissing($attribute, $value, $parameters)
    {
        return ! Arr::has($this->data, $attribute);
    }

    /**
     * Validate that an attribute is missing when another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMissingIf($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'missing_if');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (in_array($other, $values, is_bool($other) || is_null($other))) {
            return $this->validateMissing($attribute, $value, $parameters);
        }

        return true;
    }

    /**
     * Validate that an attribute is missing unless another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMissingUnless($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'missing_unless');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (! in_array($other, $values, is_bool($other) || is_null($other))) {
            return $this->validateMissing($attribute, $value, $parameters);
        }

        return true;
    }

    /**
     * Validate that an attribute is missing when any given attribute is present.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMissingWith($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'missing_with');

        if (Arr::hasAny($this->data, $parameters)) {
            return $this->validateMissing($attribute, $value, $parameters);
        }

        return true;
    }

    /**
     * Validate that an attribute is missing when all given attributes are present.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMissingWithAll($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'missing_with_all');

        if (Arr::has($this->data, $parameters)) {
            return $this->validateMissing($attribute, $value, $parameters);
        }

        return true;
    }

    /**
     * Validate the value of an attribute is a multiple of a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateMultipleOf($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'multiple_of');

        if (! $this->validateNumeric($attribute, $value, []) || ! $this->validateNumeric($attribute, $parameters[0], [])) {
            return false;
        }

        try {
            $numerator = BigDecimal::of($this->trim($value));
            $denominator = BigDecimal::of($this->trim($parameters[0]));

            if ($numerator->isZero() && $denominator->isZero()) {
                return false;
            }

            if ($numerator->isZero()) {
                return true;
            }

            if ($denominator->isZero()) {
                return false;
            }

            return $numerator->remainder($denominator)->isZero();
        } catch (BrickMathException $e) {
            throw new MathException('An error occurred while handling the multiple_of input values.', previous: $e);
        }
    }

    /**
     * "Indicate" validation should pass if value is null.
     *
     * Always returns true, just lets us put "nullable" in rules.
     *
     * @return bool
     */
    public function validateNullable()
    {
        return true;
    }

    /**
     * Validate an attribute is not contained within a list of values.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateNotIn($attribute, $value, $parameters)
    {
        return ! $this->validateIn($attribute, $value, $parameters);
    }

    /**
     * Validate that an attribute is numeric.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array{0: 'strict'}  $parameters
     * @return bool
     */
    public function validateNumeric($attribute, $value, array $parameters)
    {
        if (($parameters[0] ?? null) === 'strict' && is_string($value)) {
            return false;
        }

        return is_numeric($value);
    }

    /**
     * Validate that an attribute exists even if not filled.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validatePresent($attribute, $value)
    {
        return Arr::has($this->data, $attribute);
    }

    /**
     * Validate that an attribute is present when another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validatePresentIf($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'present_if');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (in_array($other, $values, is_bool($other) || is_null($other))) {
            return $this->validatePresent($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute is present unless another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validatePresentUnless($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'present_unless');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (! in_array($other, $values, is_bool($other) || is_null($other))) {
            return $this->validatePresent($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute is present when any given attribute is present.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validatePresentWith($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'present_with');

        if (Arr::hasAny($this->data, $parameters)) {
            return $this->validatePresent($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute is present when all given attributes are present.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validatePresentWithAll($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'present_with_all');

        if (Arr::has($this->data, $parameters)) {
            return $this->validatePresent($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute passes a regular expression check.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateRegex($attribute, $value, $parameters)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        $this->requireParameterCount(1, $parameters, 'regex');

        return preg_match($parameters[0], $value) > 0;
    }

    /**
     * Validate that an attribute does not pass a regular expression check.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateNotRegex($attribute, $value, $parameters)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        $this->requireParameterCount(1, $parameters, 'not_regex');

        return preg_match($parameters[0], $value) < 1;
    }

    /**
     * Validate that a required attribute exists.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateRequired($attribute, $value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_countable($value) && count($value) < 1) {
            return false;
        } elseif ($value instanceof File) {
            return (string) $value->getPath() !== '';
        }

        return true;
    }

    /**
     * Validate that an attribute exists when another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateRequiredIf($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'required_if');

        if (! Arr::has($this->data, $parameters[0])) {
            return true;
        }

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (in_array($other, $values, is_bool($other) || is_null($other))) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute exists when another attribute was "accepted".
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateRequiredIfAccepted($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'required_if_accepted');

        if ($this->validateAccepted($parameters[0], $this->getValue($parameters[0]))) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute exists when another attribute was "declined".
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateRequiredIfDeclined($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'required_if_declined');

        if ($this->validateDeclined($parameters[0], $this->getValue($parameters[0]))) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute does not exist or is an empty string.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateProhibited($attribute, $value)
    {
        return ! $this->validateRequired($attribute, $value);
    }

    /**
     * Validate that an attribute does not exist when another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateProhibitedIf($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'prohibited_if');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (in_array($other, $values, is_bool($other) || is_null($other))) {
            return ! $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute does not exist when another attribute was "accepted".
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateProhibitedIfAccepted($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'prohibited_if_accepted');

        if ($this->validateAccepted($parameters[0], $this->getValue($parameters[0]))) {
            return $this->validateProhibited($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute does not exist when another attribute was "declined".
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateProhibitedIfDeclined($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'prohibited_if_declined');

        if ($this->validateDeclined($parameters[0], $this->getValue($parameters[0]))) {
            return $this->validateProhibited($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute does not exist unless another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateProhibitedUnless($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'prohibited_unless');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (! in_array($other, $values, is_bool($other) || is_null($other))) {
            return ! $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that other attributes do not exist when this attribute exists.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateProhibits($attribute, $value, $parameters)
    {
        if ($this->validateRequired($attribute, $value)) {
            foreach ($parameters as $parameter) {
                if ($this->validateRequired($parameter, Arr::get($this->data, $parameter))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Indicate that an attribute is excluded.
     *
     * @return bool
     */
    public function validateExclude()
    {
        return false;
    }

    /**
     * Indicate that an attribute should be excluded when another attribute has a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateExcludeIf($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'exclude_if');

        if (! Arr::has($this->data, $parameters[0])) {
            return true;
        }

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        return ! in_array($other, $values, is_bool($other) || is_null($other));
    }

    /**
     * Indicate that an attribute should be excluded when another attribute does not have a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateExcludeUnless($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'exclude_unless');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        return in_array($other, $values, is_bool($other) || is_null($other));
    }

    /**
     * Validate that an attribute exists when another attribute does not have a given value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateRequiredUnless($attribute, $value, $parameters)
    {
        $this->requireParameterCount(2, $parameters, 'required_unless');

        [$values, $other] = $this->parseDependentRuleParameters($parameters);

        if (! in_array($other, $values, is_bool($other) || is_null($other))) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Indicate that an attribute should be excluded when another attribute presents.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateExcludeWith($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'exclude_with');

        if (! Arr::has($this->data, $parameters[0])) {
            return true;
        }

        return false;
    }

    /**
     * Indicate that an attribute should be excluded when another attribute is missing.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateExcludeWithout($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'exclude_without');

        if ($this->anyFailingRequired($parameters)) {
            return false;
        }

        return true;
    }

    /**
     * Prepare the values and the other value for validation.
     *
     * @param  array<int, int|string>  $parameters
     * @return array
     */
    public function parseDependentRuleParameters($parameters)
    {
        $other = Arr::get($this->data, $parameters[0]);

        $values = array_slice($parameters, 1);

        if ($this->shouldConvertToBoolean($parameters[0]) || is_bool($other)) {
            $values = $this->convertValuesToBoolean($values);
        }

        if (is_null($other)) {
            $values = $this->convertValuesToNull($values);
        }

        return [$values, $other];
    }

    /**
     * Check if parameter should be converted to boolean.
     *
     * @param  string  $parameter
     * @return bool
     */
    protected function shouldConvertToBoolean($parameter)
    {
        return in_array('boolean', $this->rules[$parameter] ?? []);
    }

    /**
     * Convert the given values to boolean if they are string "true" / "false".
     *
     * @param  array  $values
     * @return array
     */
    protected function convertValuesToBoolean($values)
    {
        return array_map(fn ($value) => match ($value) {
            'true' => true,
            'false' => false,
            default => $value,
        }, $values);
    }

    /**
     * Convert the given values to null if they are string "null".
     *
     * @param  array  $values
     * @return array
     */
    protected function convertValuesToNull($values)
    {
        return array_map(function ($value) {
            return Str::lower($value) === 'null' ? null : $value;
        }, $values);
    }

    /**
     * Validate that an attribute exists when any other attribute exists.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateRequiredWith($attribute, $value, $parameters)
    {
        if (! $this->allFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute exists when all other attributes exist.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateRequiredWithAll($attribute, $value, $parameters)
    {
        if (! $this->anyFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute exists when another attribute does not.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateRequiredWithout($attribute, $value, $parameters)
    {
        if ($this->anyFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Validate that an attribute exists when all other attributes do not.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validateRequiredWithoutAll($attribute, $value, $parameters)
    {
        if ($this->allFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }

    /**
     * Determine if any of the given attributes fail the required test.
     *
     * @param  array  $attributes
     * @return bool
     */
    protected function anyFailingRequired(array $attributes)
    {
        foreach ($attributes as $key) {
            if (! $this->validateRequired($key, $this->getValue($key))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if all of the given attributes fail the required test.
     *
     * @param  array  $attributes
     * @return bool
     */
    protected function allFailingRequired(array $attributes)
    {
        foreach ($attributes as $key) {
            if ($this->validateRequired($key, $this->getValue($key))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate that two attributes match.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateSame($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'same');

        $other = Arr::get($this->data, $parameters[0]);

        return $value === $other;
    }

    /**
     * Validate the size of an attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateSize($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'size');

        try {
            return BigNumber::of($this->getSize($attribute, $value))->isEqualTo($this->trim($parameters[0]));
        } catch (MathException) {
            return false;
        }
    }

    /**
     * "Validate" optional attributes.
     *
     * Always returns true, just lets us put sometimes in rules.
     *
     * @return bool
     */
    public function validateSometimes()
    {
        return true;
    }

    /**
     * Validate the attribute starts with a given substring.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateStartsWith($attribute, $value, $parameters)
    {
        return Str::startsWith($value, $parameters);
    }

    /**
     * Validate the attribute does not start with a given substring.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDoesntStartWith($attribute, $value, $parameters)
    {
        return ! Str::startsWith($value, $parameters);
    }

    /**
     * Validate the attribute ends with a given substring.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateEndsWith($attribute, $value, $parameters)
    {
        return Str::endsWith($value, $parameters);
    }

    /**
     * Validate the attribute does not end with a given substring.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int|string>  $parameters
     * @return bool
     */
    public function validateDoesntEndWith($attribute, $value, $parameters)
    {
        return ! Str::endsWith($value, $parameters);
    }

    /**
     * Validate that an attribute is a string.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateString($attribute, $value)
    {
        return is_string($value);
    }

    /**
     * Validate that an attribute is a valid timezone.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<string, null|string>  $parameters
     * @return bool
     */
    public function validateTimezone($attribute, $value, $parameters = [])
    {
        return in_array($value, timezone_identifiers_list(
            constant(DateTimeZone::class.'::'.Str::upper($parameters[0] ?? 'ALL')),
            isset($parameters[1]) ? Str::upper($parameters[1]) : null,
        ), true);
    }

    /**
     * Validate that an attribute is a valid URL.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, string>  $parameters
     * @return bool
     */
    public function validateUrl($attribute, $value, $parameters = [])
    {
        return Str::isUrl($value, $parameters);
    }

    /**
     * Validate that an attribute is a valid ULID.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validateUlid($attribute, $value)
    {
        return Str::isUlid($value);
    }

    /**
     * Validate that an attribute is a valid UUID.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array<int, int<0, 8>|'max'>  $parameters
     * @return bool
     */
    public function validateUuid($attribute, $value, $parameters)
    {
        $version = null;

        if ($parameters !== null && count($parameters) === 1) {
            $version = $parameters[0];

            if ($version !== 'max') {
                $version = (int) $parameters[0];
            }
        }

        return Str::isUuid($value, $version);
    }

    /**
     * Get the size of an attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return int|string
     */
    protected function getSize($attribute, $value)
    {
        $hasNumeric = $this->hasRule($attribute, $this->numericRules);

        // This method will determine if the attribute is a number, string, or file and
        // return the proper size accordingly. If it is a number, then number itself
        // is the size. If it is a file, we take kilobytes, and for a string the
        // entire length of the string will be considered the attribute size.
        if (is_numeric($value) && $hasNumeric) {
            return (string) $this->ensureExponentWithinAllowedRange($attribute, $this->trim($value));
        } elseif (is_array($value)) {
            return count($value);
        } elseif ($value instanceof File) {
            return (string) ($value->getSize() / 1024);
        }

        return mb_strlen($value ?? '');
    }

    /**
     * Check that the given value is a valid file instance.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isValidFileInstance($value)
    {
        if ($value instanceof UploadedFile && ! $value->isValid()) {
            return false;
        }

        return $value instanceof File;
    }

    /**
     * Determine if a comparison passes between the given values.
     *
     * @param  mixed  $first
     * @param  mixed  $second
     * @param  string  $operator
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    protected function compare($first, $second, $operator)
    {
        return match ($operator) {
            '<' => $first < $second,
            '>' => $first > $second,
            '<=' => $first <= $second,
            '>=' => $first >= $second,
            '=' => $first == $second,
            default => throw new InvalidArgumentException,
        };
    }

    /**
     * Parse named parameters to $key => $value items.
     *
     * @param  array<int, int|string>  $parameters
     * @return array
     */
    public function parseNamedParameters($parameters)
    {
        return array_reduce($parameters, function ($result, $item) {
            [$key, $value] = array_pad(explode('=', $item, 2), 2, null);

            $result[$key] = $value;

            return $result;
        });
    }

    /**
     * Require a certain number of parameters to be present.
     *
     * @param  int  $count
     * @param  array<int, int|string>  $parameters
     * @param  string  $rule
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function requireParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }

    /**
     * Check if the parameters are of the same type.
     *
     * @param  mixed  $first
     * @param  mixed  $second
     * @return bool
     */
    protected function isSameType($first, $second)
    {
        return gettype($first) == gettype($second);
    }

    /**
     * Adds the existing rule to the numericRules array if the attribute's value is numeric.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return void
     */
    protected function shouldBeNumeric($attribute, $rule)
    {
        if (is_numeric($this->getValue($attribute))) {
            $this->numericRules[] = $rule;
        }
    }

    /**
     * Trim the value if it is a string.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function trim($value)
    {
        return is_string($value) ? trim($value) : (string) $value;
    }

    /**
     * Ensure the exponent is within the allowed range.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return mixed
     *
     * @throws \Illuminate\Support\Exceptions\MathException
     */
    protected function ensureExponentWithinAllowedRange($attribute, $value)
    {
        $stringValue = (string) $value;

        if (! is_numeric($value) || ! Str::contains($stringValue, 'e', ignoreCase: true)) {
            return $value;
        }

        $scale = (int) (Str::contains($stringValue, 'e')
            ? Str::after($stringValue, 'e')
            : Str::after($stringValue, 'E'));

        $withinRange = (
            $this->ensureExponentWithinAllowedRangeUsing ?? fn ($scale) => $scale <= 1000 && $scale >= -1000
        )($scale, $attribute, $value);

        if (! $withinRange) {
            throw new MathException('Scientific notation exponent outside of allowed range.');
        }

        return $value;
    }
}
