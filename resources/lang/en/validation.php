<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Please confirm you’ve accepted the :attribute.',
    'active_url' => 'Please enter a valid URL.',
    'after' => 'Check the date! :attribute must be after :date.',
    'after_or_equal' => 'Check the date! :attribute must be on or after :date.',
    'alpha' => 'Only letters allowed here!',
    'alpha_dash' => 'Only letters, numbers, dashes and undescores allowed here!',
    'alpha_num' => 'Only letters and numbers allowed here!',
    'array' => 'The :attribute must be an array.',
    'before' => 'Check the date! The :attribute must be before :date.',
    'before_or_equal' => 'Check the date! The :attribute must be before or on :date.',
    'between' => [
        'numeric' => 'Sorry, the :attribute must be between :min and :max.',
        'file' => 'Sorry, the :attribute must be between :min and :max kilobytes.',
        'string' => 'Sorry, the :attribute must be between :min and :max characters.',
        'array' => 'Sorry, the :attribute must be between :min and :max items.',
    ],
    'boolean' => 'Please check again - the :attribute field must be true or false.',
    'confirmed' => 'Please check again - the :attribute confirmation does not match.',
    'date' => 'Sorry, the :attribute is not a valid date.',
    'date_equals' => 'Check the date! The :attribute must be a date equal to :date.',
    'date_format' => 'Please check again - the :attribute must match the format :format.',
    'different' => 'Please check again - the :attribute and :other must be different.',
    'digits' => 'Sorry, the :attribute must be :digits digits.',
    'digits_between' => 'Sorry, the :attribute must be between :min and :max digits.',
    'dimensions' => 'Sorry, the :attribute has wrong image dimensions.',
    'distinct' => 'Sorry, the :attribute field can’t match another field.',
    'email' => 'Please check again - the :attribute doesn’t seem to be a valid email address.',
    'ends_with' => 'Sorry, the :attribute must end with one of the following: :values.',
    'exists' => 'Sorry, the selected :attribute is invalid.',
    'file' => 'Please attach a file to the :attribute.',
    'filled' => 'You missed the :attribute field!',
    'gt' => [
        'numeric' => 'Go higher! The :attribute must be greater than :value.',
        'file' => 'Think bigger! The :attribute must be larger than :value kilobytes.',
        'string' => 'Please try again - the :attribute must be more than :value characters.',
        'array' => 'Please try again - the :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'Please try again - the :attribute must be greater than or equal to :value.',
        'file' => 'Think bigger! The :attribute must be larger than or equal to :value kilobytes.',
        'string' => 'Please try again - the :attribute must be greater than or equal to :value characters.',
        'array' => 'Please try again - the :attribute must have :value items or more.',
    ],
    'image' => 'Please check again - the :attribute must be an image.',
    'in' => 'Something’s wrong - the selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'Positive or negative - but the :attribute must be an integer!',
    'ip' => 'Please check again - the :attribute must be a valid IP address.',
    'ipv4' => 'Please check again - the :attribute must be a valid IPv4 address.',
    'ipv6' => 'Please check again - the :attribute must be a valid IPv6 address.',
    'json' => 'Please check again - the :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'You went a bit too big! The :attribute must be less than :value.',
        'file' => 'You went a bit too big! The :attribute must be smaller than :value kilobytes.',
        'string' => 'Sometimes less is more! The :attribute must be less than :value characters.',
        'array' => 'Sometimes less is more! The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'Sometimes less is more! The :attribute must be less than or equal :value.',
        'file' => 'You went a bit too big! The :attribute must be smaller than or equal :value kilobytes.',
        'string' => 'Sometimes less is more! The :attribute must be less than or equal :value characters.',
        'array' => 'Sometimes less is more! The :attribute must not exceed :value items.',
    ],
    'max' => [
        'numeric' => 'You went a bit too big! The :attribute may not be more than :max.',
        'file' => 'You went a bit too big! The :attribute may not be bigger than :max kilobytes.',
        'string' => 'Sometimes less is more! The :attribute may not be longer than :max characters.',
        'array' => 'Sometimes less is more! The :attribute may not have more than :max items.',
    ],
    'mimes' => 'Sorry, we need a file of type: :values here!',
    'mimetypes' => 'Sorry, we need a file of type: :values here!',
    'min' => [
        'numeric' => 'Go higher! The :attribute must be at least :min.',
        'file' => 'Think bigger! The :attribute must be at least :min kilobytes.',
        'string' => 'Give us a little more! The :attribute must be at least :min characters.',
        'array' => 'Give us a little more! The :attribute must have at least :min items.',
    ],
    'not_in' => 'Try again - the selected :attribute isn’t quite right.',
    'not_regex' => 'Sorry, :attribute isn’t in the right format.',
    'numeric' => 'Only numbers allowed here!',
    'password' => 'The password is incorrect.',
    'present' => 'We can’t continue without the :attribute!',
    'regex' => 'Sorry, :attribute isn’t in the right format.',
    'required' => 'Oops, you missed the :attribute field!',
    'required_if' => 'The :other is :value, so we also need a :attribute.',
    'required_unless' => 'We need :attribute unless :other is in :values.',
    'required_with' => 'You entered :values, so we also need a :attribute.',
    'required_with_all' => 'You entered :values, so we also need a :attribute.',
    'required_without' => 'You didn’t provide :values, so we also need a :attribute.',
    'required_without_all' => 'You didn’t provide :values, so we also need a :attribute.',
    'same' => 'Sorry - :attribute and :other must be the same.',
    'size' => [
        'numeric' => 'Sorry to be picky - the :attribute must be :size.',
        'file' => 'Sorry to be picky - the :attribute must be :size kilobytes.',
        'string' => 'Sorry to be picky - the :attribute must be :size characters.',
        'array' => 'Sorry to be picky - the :attribute must contain :size items.',
    ],
    'starts_with' => 'Start the :attribute with one of these :values',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'Sorry, this :attribute has already been taken!',
    'uploaded' => 'The :attribute didn’t upload - try again?',
    'url' => 'Sorry, this :attribute isn’t a URL!',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'geolocation' => [
            'enabled' => 'Let us find you - turn on geolocation!',
            'not_found' => 'We must be lost - location not found.',
            'no_results' => 'Sorry, we got nothing!',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
