<?php

namespace Modules\ZentroOwnerBot\Entities;

class sfSecurity
{

    const ENCRYPTION_METHOD = "AES-256-CBC";
    const EXPORT_KEY = '81092719101d435v556z';

    public static function getServerUniqueINFO()
    {
        $unique = '';
        $array = array();
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("wmic DISKDRIVE get SerialNumber", $output);
            unset($output[0]);
            foreach ($output as $key => $value) {
                if (trim($value) == '') {
                    unset($output[$key]);
                    continue;
                }
                $output[$key] = trim($value);
                $output[$key] = str_replace('-', '', $output[$key]);
            }
            $array = $output;
        } else {
            //VB062294b42d96cfed-VB8755e7433b7d41af
            exec("ls -l /dev/disk/by-id/", $output);
            foreach ($output as $line)
                if (preg_match("/(.*)_HARDDISK_(.*)/", $line)) {
                    $unique = explode('_HARDDISK_', $line);
                    $unique = explode(' ', $unique[1]);
                    $array[] = trim($unique[0]);
                }

            foreach ($array as $key => $value)
                if (stripos($value, '-part') > -1)
                    unset($array[$key]);
                else
                    $array[$key] = str_replace('-', '', $value);
        }
        $unique = implode('-', $array);
        //$unique = base64_encode(implode('-', $array));
        return $unique;
    }

    public static function getSystemInstallDate($module = 'db')
    {
        return filemtime(Util::getRootPath('', true) . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . 'actions.class.php');
    }

    public static function getSystemCheckSum()
    {
        $tochecksum = array(
            Util::getRootPath('', true) . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . 'actions.class.php',
            Util::getRootPath('', true) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'util' . DIRECTORY_SEPARATOR . 'sfSecurity.class',
            Util::getRootPath('', true) . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . '_init.js',
            Util::getRootPath('', true) . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . '_install.js'
        );

        $response = nl2br(md5_file($tochecksum[0]) . md5_file($tochecksum[1]));
        return $response;
    }

    public static function generateRegistrationCode($str, $period = '', $build = '')
    {
        $str = str_split(strtoupper(md5($str)), 4);
        $index = ord($str[count($str) - 1]) % count($str);

        if ($build != '') {
            for ($i = count($str) - 1; $i >= $index; $i--)
                $str[$i + 1] = $str[$i];
            $str[$index] = self::generateIntelligentBlocks(Util::switchTextFormatToFormat($build, "normal", "hex"));
        }
        if ($period != '') {
            for ($i = count($str) - 1; $i >= $index; $i--)
                $str[$i + 1] = $str[$i];
            $str[$index] = self::generateIntelligentBlocks(Util::switchTextFormatToFormat($period, "normal", "hex"));
        }

        return implode('-', $str);
    }

    public static function generateIntelligentBlocks($text, $length = 4)
    {
        while ((strlen($text) + 1) % $length != 0) {
            $text = str_pad($text, strlen($text) + 1, "0", STR_PAD_LEFT);
        }
        $text = ((strlen($text) + 1) / $length) . $text;
        $text = str_split($text, $length);
        return implode('-', $text);
    }

    public static function decodeIntelligentBlocks($text)
    {
        $array = array(
            'text' => $text,
            'cleantext' => '',
            'firstblock' => '',
            'blocks' => array(),
        );

        $keys = explode('-', $text);
        $index = ord(substr($keys[count($keys) - 1], 0, 1)) % 8;
        $array['firstblock'] = $keys[$index];

        $extrablocks = $keys[$index][0];
        for ($i = 0; $i < $extrablocks; $i++) {
            array_push($array['blocks'], $keys[$index + $i]);
            unset($keys[$index + $i]);
        }

        $array['cleantext'] = implode('-', $keys);

        return $array;
    }

    public static function validateRegistrationCode($appkey)
    {
        $result = array('success' => false);


        $keys = explode('-', $appkey);
        $blocks = array();
        while (count($keys) > 8) {
            $array = self::decodeIntelligentBlocks($appkey);

            array_push($blocks, $array['blocks']);
            $appkey = $array['cleantext'];
            $keys = explode('-', $appkey);
        }

        switch (count($blocks)) {
            case 2:
                //xxxx-period-build-xxxx
                $text = implode('', $blocks[1]);
                $text[0] = "0";
                $result['build'] = Util::switchTextFormatToFormat($text, "hex", "normal");
            case 1:
                $text = implode('', $blocks[0]);
                $text[0] = "0";
                $result['given'] = Util::switchTextFormatToFormat($text, "hex", "normal");

                try {
                    //xxxx-period-xxxx
                    $installdate = date_create_from_format('Y-m-d', date("Y-m-d", self::getSystemInstallDate('admin')));
                    $result['expirationdate'] = $installdate->add(new DateInterval('P' . trim($result['given'])))->format("d/m/Y");
                } catch (Exception $exc) {
                    //xxxx-build-xxxx
                    $result['expirationdate'] = null;
                    $result['build'] = $result['given'];
                    $result['given'] = null;
                }
                break;
            default:
                break;
        }

        if ($appkey == self::generateRegistrationCode(strtoupper(Util::getMetadataValue('app_name') . self::getServerUniqueINFO())))
            $result['success'] = true;

        return $result;
    }

    public static function validateRegistrationHash($appkey)
    {
        $array = array(
            "success" => true,
            "code" => "",
        );

        $validation = self::validateRegistrationCode($appkey);
        if ($validation['expirationdate'] && $validation['expirationdate'] != "")
            $expirationdate = date_create_from_format('d/m/Y', $validation['expirationdate']);
        else
            return $array;

        // if expirationdate > datenow
        $array["success"] = $array["success"] && Util::dateDifference(date('Y-m-d H:i:s'), $expirationdate->format('Y-m-d H:i:s')) > 0;
        if (!$array["success"]) {
            $array["code"] = "expirationdate";
            return $array;
        }
        // if datenow > installdate
        $array["success"] = $array["success"] && Util::dateDifference(date("Y-m-d H:i:s", self::getSystemInstallDate('admin')), date('Y-m-d H:i:s')) > 0;
        if (!$array["success"]) {
            $array["code"] = "installdate";
            return $array;
        }
        // if datenow >= errordate
        $remfile = Util::getRootPath('/apps/backend/config/rem.yml', true);
        $remining = php_strip_whitespace($remfile);
        if ($remining && $remining != "") {
            $remining = self::decrypt($remining, Util::generateCode(Util::getMetadataValue("app_name")));

            if (strpos($remining, '-') > -1 && strpos($remining, ':') > -1) {
                $errordate = date_create_from_format('Y-m-d H:i:s', $remining);
                $array["success"] = $array["success"] && Util::dateDifference($errordate->format('Y-m-d H:i:s'), date('Y-m-d H:i:s')) >= 0;
            } else
                $array["success"] = false;

            if (!$array["success"]) {
                $array["code"] = "errordate";
                return $array;
            }
        }

        return $array;
    }

    public static function validateCheckSum()
    {
        if (self::getSystemCheckSum() == Util::getMetadataValue('app_fileintegrity'))
            return true;
        return false;
    }

    public static function encrypt($text, $password, $encodepassword = true)
    {
        if ($encodepassword)
            $password = substr(hash('sha256', $password, true), 0, 32);
        return openssl_encrypt($text, sfSecurity::ENCRYPTION_METHOD, $password);
    }

    public static function decrypt($text, $password, $encodepassword = true)
    {
        if ($encodepassword)
            $password = substr(hash('sha256', $password, true), 0, 32);
        return openssl_decrypt($text, sfSecurity::ENCRYPTION_METHOD, $password);
    }

}