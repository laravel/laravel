<?php

namespace Modules\ZentroOwnerBot\Entities;

class Util
{

    // Date functions ----------------------------------------------------------
    // Convert date formats FROM - TO
    public static function convertToDate($date, $from, $to = false)
    {
        if ($date) {
            $date = date_create_from_format($from, $date);

            if ($to)
                return $date->format($to);

            return $date;
        }
        return false;
    }

    // Returns diff between dates in format Y-m-d H:i:s. Result will given in days if format = d|D, in hours if format = h|H, etc.
    public static function dateDifference($start = '2009-12-01 00:00:00', $end = '2009-12-31 00:00:00', $format = 'S')
    {
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $start);
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $end);
        $interval = $start->diff($end);

        $result = 0;
        switch (strtoupper($format)) {
            case 'D':
                $result = $interval->format('%r') . $interval->format('%a');
                break;
            case 'H':
                $result = $interval->format('%r') . ($interval->format('%a') * 24 + $interval->format('%h'));
                break;
            case 'M':
                $result = $interval->format('%r') . ($interval->format('%a') * 24 * 60 + $interval->format('%h') * 60 + $interval->format('%i'));
                break;
            case 'S':
                $result = $interval->format('%r') . ($interval->format('%a') * 24 * 60 * 60 + $interval->format('%h') * 60 * 60 + $interval->format('%i') * 60 + $interval->format('%s'));
                break;
            default:
                break;
        }

        return $result;
    }

    // Dir & Files functions ---------------------------------------------------
    public static function removeDirectory($dirname)
    {
        $response = true;
        if (is_dir($dirname)) {
            $objects = scandir($dirname);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dirname . "/" . $object) == "dir")
                        $response = $response && Util::removeDirectory($dirname . "/" . $object);
                    else
                        $response = $response && unlink($dirname . "/" . $object);
                }
            }
            reset($objects);
            $response = $response && rmdir($dirname);
        }
        return $response;
    }

    public static function removeFile($object)
    {
        $response = true;
        if (is_file($object) && object != "." && $object != "..")
            $response = $response && unlink($object);
        else
            $response = $response && Util::removeDirectory($object);

        return $response;
    }

    public static function getExtensionFromType($type = "")
    {
        switch ($type) {
            case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
                $type = 'docx';
                break;
            case 'msword':
                $type = 'doc';
                break;
            case 'vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $type = 'xlsx';
                break;
            case 'vnd.ms-excel':
                $type = 'xls';
                break;
            case 'text/html':
                $type = 'html';
                break;
            case 'pdf':
                break;
            default:
                $type = 'txt';
                break;
        }
        return $type;
    }

    public static function getTypeFromExtension($type = "")
    {
        switch ($type) {
            case 'docx':
                $type = 'vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            case 'doc':
                $type = 'msword';
                break;
            case 'xlsx':
                $type = 'vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'xls':
                $type = 'vnd.ms-excel';
                break;
            case 'html':
                $type = 'text/html';
                break;
            case 'pdf':
                break;
            default:
                $type = 'text/plain';
                break;
        }
        return $type;
    }

    public static function getRootPath($path = "", $extractupload = false, $extractimages = false)
    {
        $root = self::normalizePath(sfConfig::get("sf_upload_dir"));
        if ($extractupload) {
            $root = str_replace('web/uploads', '', $root);
            $root = str_replace('web\uploads', '', $root);
        }
        if ($extractimages)
            $root = str_replace('images/../../', '', $root);

        $path = $root . DIRECTORY_SEPARATOR . $path;

        return self::normalizePath($path);
    }

    public static function normalizePath($path, $separator = false)
    {
        if (!$separator)
            $separator = DIRECTORY_SEPARATOR;
        $path = str_replace("/", $separator, $path);
        $path = str_replace($separator . $separator, $separator, $path);

        return $path;
    }

    public static function getFileContent($file, $type = false, $returnas = 'text')
    {
        $content = '';

        if (!$type) {
            $type = explode(".", $file);
            $type = $type[count($type) - 1];
        }

        $readtype = strtolower(Util::getMetadataValue('app_filereadcontent'));

        $location = Util::getRootPath($file, true, true);

        switch (strtolower($type)) {
            case 'docx':
                $content = PHPWord_IOFactory::load($location);
                break;
            case 'pdf':
                if ($readtype == 'all') {
                    $xpdf = '';
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                        $xpdf = Util::getRootPath('lib/util/xpdf/windows/pdftohtml.exe', true, true);
                    else
                        $xpdf = Util::getRootPath('lib/util/xpdf/linux/pdftohtml', true, true);

                    $tmplocation = str_ireplace('.pdf', '', $location);

                    $command = '"' . $xpdf . '" "' . $location . '" "' . $tmplocation . '"';
                    exec($command);

                    $files = scandir($tmplocation);
                    $array = array();
                    foreach ($files as $file) {
                        if ($file != "." && $file != ".." && $file != "index.html") {
                            $key = explode('.', $file);
                            if (!isset($array[$key[0]]))
                                $array[$key[0]] = array();
                            $array[$key[0]][$key[1]] = $file;
                        }
                    }

                    foreach ($array as $value) {
                        $html = file_get_contents($value['html']);
                        $html = SimpleHtmlDom::file_get_html($tmplocation . "/" . $value['html']);

                        foreach ($html->find('body') as $e) {
                            $item = str_replace('<body onload="start()">', '', $e);
                            $item = str_replace('</body>', '', $e);

                            $img = file_get_contents($tmplocation . "/" . $value['png']);
                            $item = str_replace($value['png'], 'data:image/png;base64,' . base64_encode($img), $e);
                            $content .= $item;
                        }
                    }
                    Util::removeDirectory($tmplocation);
                } else {
                    $xpdf = '';
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                        $xpdf = Util::getRootPath('lib/util/xpdf/windows/pdftotext.exe', true, true);
                    else
                        $xpdf = Util::getRootPath('lib/util/xpdf/linux/pdftotext', true, true);


                    $command = '"' . $xpdf . '" "' . $location . '" "' . $location . '.txt"';
                    $command = str_replace('\"', '"', $command);
                    exec($command);

                    $content = file_get_contents($location . '.txt');
                    unlink($location . ".txt");
                }

                $content = utf8_encode($content);
                break;

            case 'xlsx':   //	Excel (OfficeOpenXML) Spreadsheet
            case 'xlsm':   //	Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
            case 'xltx':   //	Excel (OfficeOpenXML) Template
            case 'xltm':   //	Excel (OfficeOpenXML) Macro Template (macros will be discarded)
            case 'xls':    //	Excel (BIFF) Spreadsheet
            case 'xlt':    //	Excel (BIFF) Template
            case 'ods':    //	Open/Libre Offic Calc
            case 'ots':    //	Open/Libre Offic Calc Template
            case 'slk':
            case 'xml':    //	Excel 2003 SpreadSheetML
            case 'csv':
                $excel = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($location))->load($location);

                switch ($returnas) {
                    case '':
                    case 'text':
                        $writer = PHPExcel_IOFactory::createWriter($excel, 'HTML');
                        for ($index = 0; $index < count($excel->getAllSheets()); $index++) {
                            $writer->setSheetIndex($index);
                            $writer->save($location . ".html");
                            $content .= '<br/><br/>' . file_get_contents($location . ".html");
                            unlink($location . ".html");
                        }

                        if ($readtype != 'all') {
                            $plaintext = SimpleHtmlDom::str_get_html($content)->plaintext;
                            if ($plaintext != '')
                                $content = SimpleHtmlDom::str_get_html($content)->plaintext;
                        }
                        break;
                    case 'array':
                        $content = array();
                        $sheets = $excel->getAllSheets();
                        foreach ($sheets as $sheet)
                            $content[] = $sheet->toArray();
                        break;
                    default:
                        break;
                }

                break;
            default:
                $content = file_get_contents($location);

                if ($readtype != 'all') {
                    $html = SimpleHtmlDom::str_get_html($content);
                    die(json_encode(array('success' => true, 'message' => $html->plaintext)));
                }
                $content = Util::getAsUTF8($content);
                break;
        }
        return $content;
    }

    public static function getFilesOnDirectory($dirname = '.')
    {
        if ($dirname == '')
            $dirname = '.';

        if (!is_dir($dirname) || !is_readable($dirname))
            return false;

        $a = array();

        $handle = opendir($dirname);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && is_readable($dirname . '/' . $file)) {
                if (is_dir($dirname . '/' . $file)) {
                    if (strpos($file, '.svn') > -1) {

                    } else {
                        $currents = self::getFilesOnDirectory($dirname . '/' . $file);
                        foreach ($currents as $current)
                            $a[] = $current;
                    }
                } else {
                    $a[] = $dirname . '/' . $file;
                }
            }
        }
        closedir($handle);

        return $a;
    }

    // Text functions ----------------------------------------------------------
    public static function matchAllReplace($pattern, $text, $function)
    {
        preg_match_all("#" . $pattern . "#sm", $text, $strings, PREG_OFFSET_CAPTURE);
        $variant = 0;
        for ($index = 0; $index < count($strings[1]); $index++) {
            $left = substr($text, 0, $strings[1][$index][1] + $variant);
            $right = substr($text, $strings[1][$index][1] + strlen($strings[1][$index][0]) + $variant);

            $code = $function($strings[1][$index][0]);

            $text = $left . $code . $right;
            $variant += strlen($code) - strlen($strings[1][$index][0]);
        }
        return $text;
    }

    public static function encodeToHexdexANDOctal($string)
    {
        $array = str_split($string);
        $octdex = '';
        for ($index = 0; $index < count($array); $index++)
            if ($index % 2 == 0)
                $octdex .= str_replace('_', '', '\_' . decoct(ord($array[$index])));
            else
                $octdex .= '\x' . dechex(ord($array[$index]));

        return $octdex;
    }

    public static function switchTextFormatToFormat($text, $from, $to, $fromseparator = "", $toseparator = "")
    {
        $array = array();
        if (!$fromseparator || $fromseparator == "")
            $array = str_split($text, 1);
        else
            $array = explode($fromseparator, $text);

        $text = "";

        switch (strtoupper($from)) {
            case "HEX":
            case "HEXADECIMAL":
                switch (strtoupper($to)) {
                    case "HEX":
                    case "HEXADECIMAL":
                        $text = implode($toseparator, $array);
                        break;
                    // asuming normal to
                    default:
                        if ($fromseparator == "")
                            $array = str_split(implode($fromseparator, $array), 2);

                        foreach ($array as $char)
                            if ($text == "")
                                $text = chr(hexdec($char));
                            else
                                $text .= $toseparator . chr(hexdec($char));
                        break;
                }
                break;
            // asuming normal from
            default:
                switch (strtoupper($to)) {
                    case "HEX":
                    case "HEXADECIMAL":
                        foreach ($array as $char)
                            if ($text == "")
                                $text = sprintf("%X", ord($char));
                            else
                                $text .= $toseparator . sprintf("%X", ord($char));
                        break;
                    // asuming normal to
                    default:
                        $text = implode($toseparator, $array);
                        break;
                }
                break;
        }

        return $text;
    }

    public static function switchTextFormat($text, $format = "chars")
    {
        $dictionary = array(
            "chars" => array(
                "Á",
                "á",
                "É",
                "é",
                "Í",
                "í",
                "Ó",
                "ó",
                "Ú",
                "ú",
                "Ü",
                "ü",
                "Ñ",
                "ñ",
                "&",
                "<",
                ">",
                "í",
                " ",
                '"',
                "'",
                "©",
                "®",
                "€",
                "¼",
                "½",
                "¾",
            ),
            "html" => array(
                "&Aacute;",
                "&aacute;",
                "&Eacute;",
                "&eacute;",
                "&Iacute;",
                "&iacute;",
                "&Oacute;",
                "&oacute;",
                "&Uacute;",
                "&uacute;",
                "&Uuml;",
                "&uuml;",
                "&Ntilde;",
                "&ntilde;",
                "&amp;",
                "&lt;",
                "&gt;",
                "&itilde;",
                "&nbsp;",
                "&quot;",
                "&apos;",
                "&copy;",
                "&reg;",
                "&euro;",
                "&frac14;",
                "&frac12;",
                "&frac34;",
            ),
            "ansi" => array(
                "Á",
                "Ã¡",
                "É",
                "Ã©",
                "Í",
                "Ã­",
                "Ó",
                "Ã³",
                "Ú",
                "Ãº",
                "Ü",
                "ü",
                "Ñ",
                "Ã±",
                "&",
                "<",
                ">",
                "í",
                " ",
                '"',
                "'",
                "©",
                "®",
                "€",
                "¼",
                "½",
                "¾",
            ),
            "unicode" => array(
                "\u00C1",
                "\u00E1",
                "\u00C9",
                "\u00E9",
                "\u00CD",
                "\u00ED",
                "\u00D3",
                "\u00F3",
                "\u00DA",
                "\u00FA",
                "\u00DC",
                "\u00FC",
                "\u00D1",
                "\u00F1",
                "\u0022",
                "\u003C",
                "\u003E",
                "\u00ED",
                "\u00A0",
                "\u0022",
                "\u0027",
                "\u00A9",
                "\u00AE",
                "\u20AC",
                "\u00BC",
                "\u00BD",
                "\u00BE",
            )
        );

        foreach ($dictionary as $key => $value)
            if ($key != $format)
                for ($index = 0; $index < count($value); $index++)
                    $text = str_replace($value[$index], $dictionary[$format][$index], $text);

        return $text;
    }

    public static function capitalize($text)
    {
        $array = str_split(strtolower($text));
        $array[0] = strtoupper($array[0]);
        return implode('', $array);
    }

    public static function getUnit($x, $y)
    {
        $array = array(
            'units' => array("", "un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve", "diez", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete", "dieciocho", "diecinueve", "veinte", "veintiún", "veintidos", "veintitres", "veinticuatro", "veinticinco", "veintiseis", "veintisiete", "veintiocho", "veintinueve"),
            'dozens' => array("", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"),
            'hundreds' => array("", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos")
        );
        return $array[$x][$y];
    }

    public static function getNumberSpell($number)
    {
        return Numbertext::numbertext($number, 'es_ES');
    }

    public static function getNumberFormated($number, $decimals = 2, $comaseparator = '.', $mileseparator = ' ')
    {
        return number_format($number, $decimals, $comaseparator, $mileseparator);
    }

    public static function getMonthName($number, $languaje = 'es')
    {
        $months = array(
            1 => array(
                'es' => 'Enero',
                'en' => 'January',
            ),
            2 => array(
                'es' => 'Febrero',
                'en' => 'February',
            ),
            3 => array(
                'es' => 'Marzo',
                'en' => 'March',
            ),
            4 => array(
                'es' => 'Abril',
                'en' => 'April',
            ),
            5 => array(
                'es' => 'Mayo',
                'en' => 'May',
            ),
            6 => array(
                'es' => 'Junio',
                'en' => 'June',
            ),
            7 => array(
                'es' => 'Julio',
                'en' => 'July',
            ),
            8 => array(
                'es' => 'Agosto',
                'en' => 'August',
            ),
            9 => array(
                'es' => 'Septiembre',
                'en' => 'September',
            ),
            10 => array(
                'es' => 'Octubre',
                'en' => 'Octuber',
            ),
            11 => array(
                'es' => 'Noviembre',
                'en' => 'November',
            ),
            12 => array(
                'es' => 'Diciembre',
                'en' => 'December',
            )
        );

        return $months[$number][$languaje];
    }

    public static function getOrdinalName($number, $languaje = 'es', $variant = 1)
    {
        $months = array(
            1 => array(
                'es' => array(
                    1 => 'Primer',
                    2 => 'Primero'
                ),
                'en' => array(
                    1 => 'First'
                ),
            ),
            2 => array(
                'es' => array(
                    1 => 'Segundo'
                ),
                'en' => array(
                    1 => 'Second'
                ),
            ),
            3 => array(
                'es' => array(
                    1 => 'Tercer',
                    2 => 'Tercero'
                ),
                'en' => array(
                    1 => 'Third'
                ),
            ),
            4 => array(
                'es' => array(
                    1 => 'Cuarto'
                ),
                'en' => array(
                    1 => 'Fourth'
                ),
            ),
            'l' => array(
                'es' => array(
                    1 => 'Ultimo',
                    2 => 'Último'
                ),
                'en' => array(
                    1 => 'Last'
                ),
            )
        );

        return $months[$number][$languaje][$variant];
    }

    public static function getDayName($number, $languaje = 'es', $variant = 1)
    {
        $days = array(
            1 => array(
                'es' => array(
                    1 => 'Lunes'
                ),
                'en' => array(
                    1 => 'Monday'
                ),
            ),
            2 => array(
                'es' => array(
                    1 => 'Martes'
                ),
                'en' => array(
                    1 => 'Tuesday'
                ),
            ),
            3 => array(
                'es' => array(
                    1 => 'Miercoles',
                    2 => 'Miércoles'
                ),
                'en' => array(
                    1 => 'Wednesday'
                ),
            ),
            4 => array(
                'es' => array(
                    1 => 'Jueves'
                ),
                'en' => array(
                    1 => 'Thursday'
                ),
            ),
            5 => array(
                'es' => array(
                    1 => 'Viernes'
                ),
                'en' => array(
                    1 => 'Friday'
                ),
            ),
            6 => array(
                'es' => array(
                    1 => 'Sabado',
                    2 => 'Sábado'
                ),
                'en' => array(
                    1 => 'Saturday'
                ),
            ),
            7 => array(
                'es' => array(
                    1 => 'Domingo'
                ),
                'en' => array(
                    1 => 'Sunday'
                ),
            )
        );

        return $days[$number][$languaje][$variant];
    }

    public static function getSummaryFromHTML($html, $query, $size = false, $hilight = false)
    {
        if (!$size)
            $size = 85;

        // getting text without HTML
        $content = SimpleHtmlDom::str_get_html($html)->plaintext;
        // formatig query as it is in the text        
        $query = substr($content, stripos($content, $query), strlen($query));

        $left = substr($content, 0, stripos($content, $query));
        $left = explode(' ', $left);
        $str = '';
        for ($index = count($left) - 4; $index < count($left); $index++)
            $str .= ' ' . $left[$index];
        $left = $str;

        $content = substr($content, stripos($content, $left), $size - strlen($left));

        if ($hilight) {
            $content = explode($query, $content);
            $content = implode('<b>' . $query . '</b>', $content);
        }

        return $content . '...';
    }

    public static function getSimilarStatistic($text1 = "", $text2 = "")
    {

        $match = similar_text(strtoupper(str_replace(" ", "", $text1)), strtoupper(str_replace(" ", "", $text2)), $percent);
        $percent = round($percent, 2);

        //        $match = rand(0, 100);
//        $percent = rand(0, 100);

        return array(
            'match' => $match,
            'percent' => $percent
        );
    }

    public static function getKeyWords($text = '', $exclude = array(), $amount = false)
    {
        $text = SimpleHtmlDom::str_get_html($text)->plaintext;

        foreach ($exclude as $word)
            $text = str_ireplace(' ' . $word . ' ', ' ', $text);

        for ($index = 0; $index < 10; $index++)
            $text = str_replace($index . '', '', $text);


        $text = str_replace('&nbsp', ' ', $text);
        $text = str_replace('(', '', $text);
        $text = str_replace(')', '', $text);
        $text = str_replace('[', '', $text);
        $text = str_replace(']', '', $text);
        $text = str_replace('{', '', $text);
        $text = str_replace('}', '', $text);
        $text = str_replace('¡', '', $text);
        $text = str_replace('!', '', $text);
        $text = str_replace('?', '', $text);
        $text = str_replace('¿', '', $text);
        $text = str_replace('.', '', $text);
        $text = str_replace(':', '', $text);
        $text = str_replace(',', '', $text);
        $text = str_replace(';', '', $text);
        $text = str_replace('_', '', $text);
        $text = str_replace('"', '', $text);
        $text = str_replace('“', '', $text);
        $text = str_replace('”', '', $text);
        $text = str_replace("'", '', $text);
        $text = str_replace("#", '', $text);
        $text = str_replace("%", '', $text);
        $text = str_replace("=", '', $text);
        $text = str_replace("*", '', $text);
        $text = str_replace("+", '', $text);
        $text = str_replace("-", '', $text);
        $text = str_replace("<", '', $text);
        $text = str_replace(">", '', $text);
        $text = str_replace(">=", '', $text);
        $text = str_replace("<=", '', $text);
        $text = str_replace("&", '', $text);

        $text = Doctrine_Inflector::unaccent($text);

        $text = explode(' ', $text);

        $text = array_count_values($text);

        // arsort($text); // si se activa se hace una comparacion en base a las mas usadas, de lo contrario en base a las menos usadas

        for ($index = 0; $index < count($exclude); $index++)
            $exclude[$index] = strtolower($exclude[$index]);

        $final = array();
        foreach ($text as $key => $value)
            if (!in_array(strtolower($key), $exclude) && strlen($key) > 3) {
                $similar = false;
                foreach ($final as $word) {
                    $statistic = Util::getSimilarStatistic($key, $word);
                    if ($statistic['percent'] > 90) {
                        $similar = true;
                        break;
                    }
                }
                if ($similar)
                    continue;

                $final[] = strtolower($key);
                if ($amount && count($final) >= $amount)
                    break;
            }

        return $final;
    }

    public static function getAsUTF8($text)
    {
        //        if (mb_check_encoding($text, "UTF-8"))
//            return $text;

        if (preg_match('!!u', $text))
            return $text;

        return utf8_encode($text);
    }

    // Servers functions -------------------------------------------------------
    public static function getIP()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            return $_SERVER['REMOTE_ADDR'];
    }

    public static function getHost()
    {
        return gethostbyaddr(self::getIP());
    }

    public static function isWindowsHost()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public static function setExecutionEnviroment($memory = false, $time = false)
    {
        $memorylimit = "512M";
        if ($memory)
            $memorylimit = $memory;

        $timelimit = "240";
        if ($time)
            $timelimit = $time;

        ini_set("safe_mode", "Off");
        ini_set("memory_limit", $memorylimit);
        ini_set("max_execution_time", $timelimit);
    }

    // Trees functions ---------------------------------------------------------
    // To walk around tree in order request (preorder, postorder)
    public static function getArrayOrdered($object, $array, $order, $method, $field = false, $ids = array())
    {
        if ($order == 'preorder')
            $array = self::pushIfNoInArray($object, $array, $field);

        $list = $object->$method();
        foreach ($list as $element) {
            if (!in_array($element->getId(), $ids)) {
                $ids[] = $element->getId();
                $array = self::getArrayOrdered($element, $array, $order, $method, $field, $ids);
            }
        }

        if ($order == 'postorder')
            $array = self::pushIfNoInArray($object, $array, $field);

        return $array;
    }

    private static function pushIfNoInArray($object, $array, $field = false)
    {
        $inarray = false;
        foreach ($array as $value)
            if ($value['id'] == $object->getId()) {
                $inarray = true;
                break;
            }

        if (!$inarray) {
            $x = $object->toArray();
            if ($field)
                $array[] = $x[$field];
            else
                $array[] = $x;
        }

        return $array;
    }

    // Framework functions -----------------------------------------------------
    // Look for a languaje content defined in web/languajes/languaje_es-ES.json
    public static function getBundle($name, $languaje = 'es-ES')
    {
        $location = Util::getRootPath('/web/languajes/languaje_' . $languaje . '.json', true, true);

        $text = file_get_contents($location, true);
        $text = preg_replace("#\#(.*?)\n#sm", "\n", $text);
        preg_match_all("#(.*?) \"(.*?)\"#sm", $text, $rows);

        $bundle = array();
        foreach ($rows[1] as $key => $value)
            $bundle[trim($value)] = trim($rows[2][$key]);

        return $bundle[$name];
    }

    // Look for a template like apps/backend/modules/mail/templates/_NotificationSuccess.php and evaluate the content to return plain HTML
    public static function getTemplateContent($params, $template)
    {
        $location = Util::getRootPath($template, true, true);

        $text = file_get_contents($location, true);
        $text = str_replace('<?php echo ', '', $text);
        $text = str_replace('; ?>', '', $text);
        $text = str_replace('?>', '', $text);
        foreach ($params as $key => $value)
            $text = str_ireplace("$" . str_replace('_', '', $key), $value, $text);

        return $text;
    }

    public static function generateCode($str)
    {
        return md5(strtoupper($str));
    }

    public static function printArrayInErrorBox($array = array())
    {
        $result = '';
        $keys = array_keys($array);
        for ($index = 0; $index < count($array); $index++) {
            $result = $result . $keys[$index] . ': ' . $array[$keys[$index]] . '<br/>';
        }
        throw new Exception($result);
    }

    public static function getMetadataValue($value)
    {
        $configrecord = Doctrine::getTable('Metadata')->find($value);
        if ($configrecord)
            return $configrecord->getValue();
        return false;
    }

    public static function sendEmail($mailer, $to, $subject, $msg, $cc = false, $bcc = false)
    {
        $mailhost = MetadataTable::getInstance()->find('app_mailhost')->toArray();
        $mailhost = $mailhost['value'];
        $mailer->getTransport()->setHost($mailhost);

        $mailhostport = MetadataTable::getInstance()->find('app_mailhostport')->toArray();
        $mailhostport = $mailhostport['value'];
        $mailer->getTransport()->setPort($mailhostport);

        $mailencryption = MetadataTable::getInstance()->find('app_mailencryption')->toArray();
        $mailencryption = $mailencryption['value'];
        if ($mailencryption != '~')
            $mailer->getTransport()->setEncryption($mailencryption);

        $mailusername = MetadataTable::getInstance()->find('app_mailusername')->toArray();
        $mailusername = $mailusername['value'];
        if ($mailusername != '~')
            $mailer->getTransport()->setUsername($mailusername);

        $mailpassword = MetadataTable::getInstance()->find('app_mailpassword')->toArray();
        $mailpassword = $mailpassword['value'];
        if ($mailpassword != '~')
            $mailer->getTransport()->setPassword($mailpassword);


        $message = $mailer->compose();
        $message->setSubject(self::switchTextFormat($subject));
        //        $message->attach(Swift_Attachment::fromPath('/ruta/hasta/el/archivo.zip'));

        $to = str_replace(' ', '', $to);
        if (stripos($to, ';')) {
            $to = explode(';', $to);
            foreach ($to as $receipt) {
                $message->addTo($receipt);
            }
        } else
            $message->setTo($to);

        if ($cc) {
            $cc = str_replace(' ', '', $cc);
            if (stripos($cc, ';')) {
                $cc = explode(';', $cc);
                foreach ($cc as $receipt) {
                    $message->addCc($receipt);
                }
            } else
                $message->addCc($cc);
        }

        if ($bcc) {
            $bcc = str_replace(' ', '', $bcc);
            if (stripos($bcc, ';')) {
                $bcc = explode(';', $bcc);
                foreach ($bcc as $receipt) {
                    $message->addBcc($receipt);
                }
            } else
                $message->addBcc($bcc);
        }

        $appname = MetadataTable::getInstance()->find('app_name')->toArray();
        $appname = $appname['value'];
        if ($mailusername != '~')
            if ($appname && $appname != '')
                $message->setFrom($mailusername, Util::switchTextFormat($appname));
            else
                $message->setFrom($mailusername);
        else {
            $appbusinessmail = MetadataTable::getInstance()->find('app_businessmail')->toArray();
            $appbusinessmail = $appbusinessmail['value'];

            $message->setFrom($appbusinessmail);
        }


        $message->setBody($msg, 'text/html');
        $mailer->send($message);
    }

    public static function getPage($query, $start = 0, $limit = 10, $searchengine = false)
    {
        self::setExecutionEnviroment();

        if (!$searchengine)
            $searchengine = array(
                'name' => 'Google Académico',
                'comment' => 'http://localhost:5800/search/gscholar.php?start=[start]&q=[query]',
                'scraplevars' => '$html,$line,$item,$limit,$languaje',
                'scraplefn' => '$about = ""; $results = ""; $comma = ""; switch ($languaje) { case "es": $about = "Aproximadamente"; $results = "resultados"; $comma = "."; break; default: $about = "About"; $results = "results"; $comma = ","; break; } preg_match_all("/" . $about . ".*?" . $results . "/i", $html, $matches, PREG_PATTERN_ORDER); preg_match_all("/[1-9](?:\d{0,2})(?:" . $comma . "\d{3})*(?:\.\d*[1-9])?|0?\.\d*[1-9]|0/i", $matches[0][0], $matches, PREG_PATTERN_ORDER); $results = array( "total" => str_replace($comma, "", $matches[0][0]) ); preg_match_all("#<div id=\"gs_ccl\" role=\"main\">(.*?)<script>#sm", $html, $main); $main = $main[0][0]; preg_match_all("#<div class=\"gs_r\">(.*?)</div></div></div>#sm", $main, $rows); foreach ($rows[0] as $row) { $item++; preg_match_all("#<a (.*?)</a>#sm", $row, $archive); $archiveref = explode(' . "'" . '"' . "'" . ', $archive[1][0]); $archiveref = $archiveref[1];  preg_match_all("#<span class=\"gs_ggsS\">(.*?)<span class=\"gs_ctg2\">(.*?)</span></span>#sm", $row, $archive); $archivename = $archive[1][0]; $archivetype = $archive[2][0]; preg_match_all("#<div class=\"gs_ri\">(.*?)</div></div>#sm", $row, $rowitem); $rowitem = $rowitem[0][0]; preg_match_all("#<h3 class=\"gs_rt\"><a (.*?)>(.*?)</a></h3>#sm", $rowitem, $header); $titlehref =  explode(' . "'" . '"' . "'" . ', $header[1][0]); $titlehref = $titlehref[1]; $title = $header[2][0]; if (!$title || $title == "") { preg_match_all("#<h3 class=\"gs_rt\"><span class=\"gs_ctc\">(.*?)</span>(.*?)</h3>#sm", $rowitem, $header); $titlehref =  explode(' . "'" . '"' . "'" . ', $header[2][0]); $title = str_replace("(strong)", "<b>", str_replace("(/strong)", "</b>", str_replace(">", "", str_replace("</b>", "(/strong)", str_replace("<b>", "(strong)", str_replace("</a>", "", $titlehref[6])))))); $titlehref = $titlehref[3]; if (!$title || $title == "") {  preg_match_all("#<h3 class=\"gs_rt\">(.*?)</h3>#sm", $rowitem, $header);  $titlehref = "";  $title = $header[1][0]; } } preg_match_all("#<div class=\"gs_a\">(.*?)</div>#sm", $rowitem, $header); $author = $header[1][0]; preg_match_all("#<div class=\"gs_rs\">(.*?)</div>#sm", $rowitem, $header); $summary = $header[0][0]; if ($item >= $line && count($results) - 1 < $limit) $results[] = array(  "title" => array(  "text" => $title,  "href" => $titlehref,  ),  "author" => $author,  "archive" => array(  "name" => $archivename,  "type" => $archivetype,  "href" => $archiveref,  ),  "summary" => $summary ); } return $results;'
            );

        $results = array(
            'total' => 0
        );

        $page = floor($start / 10);
        $firstitem = $start % 10;

        $total = 999999;

        $totaled = false;
        while ($limit > 0 && $total > 0) {
            $url = str_replace("[query]", urlencode($query), str_replace("[start]", $page, $searchengine['comment']));
            //throw new Exception($url);

            $html = file_get_contents($url);

            // deleting car returns and line jumps
            $html = str_replace("\n", "", $html);
            $html = str_replace("\r", "", $html);
            // deleting duplicated spaces
            $html = preg_replace('/\s\s+/', '', $html);

            $scraple = create_function($searchengine['scraplevars'], $searchengine['scraplefn']);
            $items = $scraple($html, $firstitem, $limit, $searchengine['comment']);
            //$items = searcherActions::scrapleGoogle($html, $firstitem, $limit, $searchengine['comment']);
            if (!$totaled) {
                $total = $items['total'];
                $results['total'] = $results['total'] + $items['total'];
                $totaled = true;
            }
            unset($items['total']);
            $results = array_merge($results, $items);

            $total -= count($items);

            $firstitem = $start + count($items);
            if ($firstitem > 9 || count($results) <= $limit) {
                $firstitem = 0;
                $page++;
            }

            $limit = $limit - count($items);
        }

        return $results;
    }

    public static function getAllCombinations($array, $includeall = true, $separator = " ")
    {
        $collect = array();
        self::depth_picker($array, $collect, $includeall, "", $separator);
        return $collect;
    }

    private static function depth_picker($array, &$collect, $includeall = true, $temp_string = "", $separator = " ")
    {
        if ($includeall && $temp_string != "")
            $collect[] = $temp_string;

        for ($i = 0, $iMax = sizeof($array); $i < $iMax; $i++) {
            $arraycopy = $array;
            $elem = array_splice($arraycopy, $i, 1); // removes and returns the i'th element
            if (sizeof($arraycopy) > 0) {
                self::depth_picker($arraycopy, $collect, $includeall, $temp_string . $separator . $elem[0]);
            } else {
                $collect[] = $temp_string . " " . $elem[0];
            }
        }
    }

}
