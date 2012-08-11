<?php

class BreadcrumbException extends \Exception {}

class Breadcrumb
{
    // Raw input array
    public $segments_raw = array();

    // Formatted array
    public $segments_translated = array();

    // language file name without extension
    private $lang_file = 'breadcrumb';

    /**
     * The constructor
     *
     * Can be initialized with custom input, or if nothing is provided
     * it tries to translate the current URI.
     *
     * @param mixed $input
     */
    public function __construct($input = null)
    {
        $this->create($input);
    }

    /**
     * Splitter method
     *
     * Splits (if it needs to) the given URI string into an array
     *
     * @param  string     $uri_string
     * @return mixed
     */
    private function split_uri($uri_string)
    {
        $pos = strpos($uri_string, '/');

        if($pos !== false)
        {
            return(explode('/', $uri_string));
        }

        return array($uri_string);
    }

    /**
     *
     * Create those breadcrumbs!
     *
     * Translates the input segments if it finds a match for them in
     * the language files, if not, it leaves them as they are.
     *
     * @param  mixed    $input
     * @param  string   $casing
     * @throws BreadcrumbException
     * @return void
     */
    public function create($input = null, $casing = 'lower')
    {

        // Check if an input was given or not and process it if it's necessary
        if(is_array($input))
        {
            $this->segments_raw = $input;
        }
        elseif($input != null)
        {
            $this->segments_raw = $this->split_uri($input);
        }
        else
        {
            $this->segments_raw = $this->split_uri(URI::current());
        }

        // Translation
        if(is_array($this->segments_raw) && !empty($this->segments_raw))
        {
            // Clean previous versions
            $this->segments_translated = null;

            foreach($this->segments_raw AS $value)
            {
                $key = $this->lang_file . '.' . $value;
                $tmp = '';

                // If the segment is in the language file it loads it, otherwise
                // keeps it unchanged
                if(Lang::has($key))
                {
                    $tmp = Lang::line($key)->get();
                }
                else
                {
                    $tmp = $value;
                }

                // Formats
                switch($casing)
                {
                    case 'lower':
                        $tmp = Str::lower($tmp);
                        break;
                    case 'upper':
                        $tmp = Str::upper($tmp);
                        break;
                    case 'title':
                        $tmp = Str::title($tmp);
                        break;
                    default:
                        $tmp = Str::lower($tmp);
                }

                $this->segments_translated[] = $tmp;
            }
        }
        else
        {
            throw new BreadcrumbException('No array provided to work with!');
        }
    }

    /**
     *
     * Dump the converted array
     *
     * Able to do its job in variable formats (default: array). It also can skip
     * elements from the start or the end of the array.
     *
     * @param  string     $format
     * @param  int        $slice_to_left        ->|
     * @param  int        $slice_from_right         |<-
     * @throws BreadcrumbException
     * @return array, json array
     */
    public function get($format = 'array', $slice_to_left = 0, $slice_from_right = 0)
    {
        $result_formatted = null;

        if(!empty($this->segments_translated))
        {
            $final_array = array();
            $max = count($this->segments_translated) - 1;

            // ignore the not needed segments
            for($key = 0 + $slice_to_left; $key <= $max - $slice_from_right; $key++)
            {
                $final_array[] = $this->segments_translated[$key];
            }

            // decide how to display output
            switch ($format)
            {
                case 'json':
                    $result_formatted = json_encode($final_array);
                    break;
                default:
                    $result_formatted = $final_array;
                    break;
            }

            return $result_formatted;
        }
        else
        {
            Throw new BreadcrumbException('Nothing to dump!');
        }
    }

}