<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * This authentication is for Exchange servers. We support version 1 & 2.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles NTLM authentication.
 *
 * @author Ward Peeters <ward@coding-tech.com>
 */
class Swift_Transport_Esmtp_Auth_NTLMAuthenticator implements Swift_Transport_Esmtp_Authenticator
{
    const NTLMSIG = "NTLMSSP\x00";
    const DESCONST = 'KGS!@#$%';

    /**
     * Get the name of the AUTH mechanism this Authenticator handles.
     *
     * @return string
     */
    public function getAuthKeyword()
    {
        return 'NTLM';
    }

    /**
     * Try to authenticate the user with $username and $password.
     *
     * @param Swift_Transport_SmtpAgent $agent
     * @param string                    $username
     * @param string                    $password
     *
     * @return bool
     */
    public function authenticate(Swift_Transport_SmtpAgent $agent, $username, $password)
    {
        if (!function_exists('mcrypt_module_open')) {
            throw new LogicException('The mcrypt functions need to be enabled to use the NTLM authenticator.');
        }

        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new LogicException('The OpenSSL extension must be enabled to use the NTLM authenticator.');
        }

        if (!function_exists('bcmul')) {
            throw new LogicException('The BCMatch functions must be enabled to use the NTLM authenticator.');
        }

        try {
            // execute AUTH command and filter out the code at the beginning
            // AUTH NTLM xxxx
            $response = base64_decode(substr(trim($this->sendMessage1($agent)), 4));

            // extra parameters for our unit cases
            $timestamp = func_num_args() > 3 ? func_get_arg(3) : $this->getCorrectTimestamp(bcmul(microtime(true), '1000'));
            $client = func_num_args() > 4 ? func_get_arg(4) : $this->getRandomBytes(8);

            // Message 3 response
            $this->sendMessage3($response, $username, $password, $timestamp, $client, $agent);

            return true;
        } catch (Swift_TransportException $e) {
            $agent->executeCommand("RSET\r\n", array(250));

            return false;
        }
    }

    protected function si2bin($si, $bits = 32)
    {
        $bin = null;
        if ($si >= -pow(2, $bits - 1) && ($si <= pow(2, $bits - 1))) {
            // positive or zero
            if ($si >= 0) {
                $bin = base_convert($si, 10, 2);
                // pad to $bits bit
                $bin_length = strlen($bin);
                if ($bin_length < $bits) {
                    $bin = str_repeat('0', $bits - $bin_length).$bin;
                }
            } else {
                // negative
                $si = -$si - pow(2, $bits);
                $bin = base_convert($si, 10, 2);
                $bin_length = strlen($bin);
                if ($bin_length > $bits) {
                    $bin = str_repeat('1', $bits - $bin_length).$bin;
                }
            }
        }

        return $bin;
    }

    /**
     * Send our auth message and returns the response.
     *
     * @param Swift_Transport_SmtpAgent $agent
     *
     * @return string SMTP Response
     */
    protected function sendMessage1(Swift_Transport_SmtpAgent $agent)
    {
        $message = $this->createMessage1();

        return $agent->executeCommand(sprintf("AUTH %s %s\r\n", $this->getAuthKeyword(), base64_encode($message)), array(334));
    }

    /**
     * Fetch all details of our response (message 2).
     *
     * @param string $response
     *
     * @return array our response parsed
     */
    protected function parseMessage2($response)
    {
        $responseHex = bin2hex($response);
        $length = floor(hexdec(substr($responseHex, 28, 4)) / 256) * 2;
        $offset = floor(hexdec(substr($responseHex, 32, 4)) / 256) * 2;
        $challenge = $this->hex2bin(substr($responseHex, 48, 16));
        $context = $this->hex2bin(substr($responseHex, 64, 16));
        $targetInfoH = $this->hex2bin(substr($responseHex, 80, 16));
        $targetName = $this->hex2bin(substr($responseHex, $offset, $length));
        $offset = floor(hexdec(substr($responseHex, 88, 4)) / 256) * 2;
        $targetInfoBlock = substr($responseHex, $offset);
        list($domainName, $serverName, $DNSDomainName, $DNSServerName, $terminatorByte) = $this->readSubBlock($targetInfoBlock);

        return array(
            $challenge,
            $context,
            $targetInfoH,
            $targetName,
            $domainName,
            $serverName,
            $DNSDomainName,
            $DNSServerName,
            $this->hex2bin($targetInfoBlock),
            $terminatorByte,
        );
    }

    /**
     * Read the blob information in from message2.
     *
     * @param $block
     *
     * @return array
     */
    protected function readSubBlock($block)
    {
        // remove terminatorByte cause it's always the same
        $block = substr($block, 0, -8);

        $length = strlen($block);
        $offset = 0;
        $data = array();
        while ($offset < $length) {
            $blockLength = hexdec(substr(substr($block, $offset, 8), -4)) / 256;
            $offset += 8;
            $data[] = $this->hex2bin(substr($block, $offset, $blockLength * 2));
            $offset += $blockLength * 2;
        }

        if (count($data) == 3) {
            $data[] = $data[2];
            $data[2] = '';
        }

        $data[] = $this->createByte('00');

        return $data;
    }

    /**
     * Send our final message with all our data.
     *
     * @param string                    $response  Message 1 response (message 2)
     * @param string                    $username
     * @param string                    $password
     * @param string                    $timestamp
     * @param string                    $client
     * @param Swift_Transport_SmtpAgent $agent
     * @param bool                      $v2        Use version2 of the protocol
     *
     * @return string
     */
    protected function sendMessage3($response, $username, $password, $timestamp, $client, Swift_Transport_SmtpAgent $agent, $v2 = true)
    {
        list($domain, $username) = $this->getDomainAndUsername($username);
        //$challenge, $context, $targetInfoH, $targetName, $domainName, $workstation, $DNSDomainName, $DNSServerName, $blob, $ter
        list($challenge, , , , , $workstation, , , $blob) = $this->parseMessage2($response);

        if (!$v2) {
            // LMv1
            $lmResponse = $this->createLMPassword($password, $challenge);
            // NTLMv1
            $ntlmResponse = $this->createNTLMPassword($password, $challenge);
        } else {
            // LMv2
            $lmResponse = $this->createLMv2Password($password, $username, $domain, $challenge, $client);
            // NTLMv2
            $ntlmResponse = $this->createNTLMv2Hash($password, $username, $domain, $challenge, $blob, $timestamp, $client);
        }

        $message = $this->createMessage3($domain, $username, $workstation, $lmResponse, $ntlmResponse);

        return $agent->executeCommand(sprintf("%s\r\n", base64_encode($message)), array(235));
    }

    /**
     * Create our message 1.
     *
     * @return string
     */
    protected function createMessage1()
    {
        return self::NTLMSIG
        .$this->createByte('01') // Message 1
.$this->createByte('0702'); // Flags
    }

    /**
     * Create our message 3.
     *
     * @param string $domain
     * @param string $username
     * @param string $workstation
     * @param string $lmResponse
     * @param string $ntlmResponse
     *
     * @return string
     */
    protected function createMessage3($domain, $username, $workstation, $lmResponse, $ntlmResponse)
    {
        // Create security buffers
        $domainSec = $this->createSecurityBuffer($domain, 64);
        $domainInfo = $this->readSecurityBuffer(bin2hex($domainSec));
        $userSec = $this->createSecurityBuffer($username, ($domainInfo[0] + $domainInfo[1]) / 2);
        $userInfo = $this->readSecurityBuffer(bin2hex($userSec));
        $workSec = $this->createSecurityBuffer($workstation, ($userInfo[0] + $userInfo[1]) / 2);
        $workInfo = $this->readSecurityBuffer(bin2hex($workSec));
        $lmSec = $this->createSecurityBuffer($lmResponse, ($workInfo[0] + $workInfo[1]) / 2, true);
        $lmInfo = $this->readSecurityBuffer(bin2hex($lmSec));
        $ntlmSec = $this->createSecurityBuffer($ntlmResponse, ($lmInfo[0] + $lmInfo[1]) / 2, true);

        return self::NTLMSIG
        .$this->createByte('03') // TYPE 3 message
.$lmSec // LM response header
.$ntlmSec // NTLM response header
.$domainSec // Domain header
.$userSec // User header
.$workSec // Workstation header
.$this->createByte('000000009a', 8) // session key header (empty)
.$this->createByte('01020000') // FLAGS
.$this->convertTo16bit($domain) // domain name
.$this->convertTo16bit($username) // username
.$this->convertTo16bit($workstation) // workstation
.$lmResponse
        .$ntlmResponse;
    }

    /**
     * @param string $timestamp  Epoch timestamp in microseconds
     * @param string $client     Random bytes
     * @param string $targetInfo
     *
     * @return string
     */
    protected function createBlob($timestamp, $client, $targetInfo)
    {
        return $this->createByte('0101')
        .$this->createByte('00')
        .$timestamp
        .$client
        .$this->createByte('00')
        .$targetInfo
        .$this->createByte('00');
    }

    /**
     * Get domain and username from our username.
     *
     * @example DOMAIN\username
     *
     * @param string $name
     *
     * @return array
     */
    protected function getDomainAndUsername($name)
    {
        if (strpos($name, '\\') !== false) {
            return explode('\\', $name);
        }

        list($user, $domain) = explode('@', $name);

        return array($domain, $user);
    }

    /**
     * Create LMv1 response.
     *
     * @param string $password
     * @param string $challenge
     *
     * @return string
     */
    protected function createLMPassword($password, $challenge)
    {
        // FIRST PART
        $password = $this->createByte(strtoupper($password), 14, false);
        list($key1, $key2) = str_split($password, 7);

        $desKey1 = $this->createDesKey($key1);
        $desKey2 = $this->createDesKey($key2);

        $constantDecrypt = $this->createByte($this->desEncrypt(self::DESCONST, $desKey1).$this->desEncrypt(self::DESCONST, $desKey2), 21, false);

        // SECOND PART
        list($key1, $key2, $key3) = str_split($constantDecrypt, 7);

        $desKey1 = $this->createDesKey($key1);
        $desKey2 = $this->createDesKey($key2);
        $desKey3 = $this->createDesKey($key3);

        return $this->desEncrypt($challenge, $desKey1).$this->desEncrypt($challenge, $desKey2).$this->desEncrypt($challenge, $desKey3);
    }

    /**
     * Create NTLMv1 response.
     *
     * @param string $password
     * @param string $challenge
     *
     * @return string
     */
    protected function createNTLMPassword($password, $challenge)
    {
        // FIRST PART
        $ntlmHash = $this->createByte($this->md4Encrypt($password), 21, false);
        list($key1, $key2, $key3) = str_split($ntlmHash, 7);

        $desKey1 = $this->createDesKey($key1);
        $desKey2 = $this->createDesKey($key2);
        $desKey3 = $this->createDesKey($key3);

        return $this->desEncrypt($challenge, $desKey1).$this->desEncrypt($challenge, $desKey2).$this->desEncrypt($challenge, $desKey3);
    }

    /**
     * Convert a normal timestamp to a tenth of a microtime epoch time.
     *
     * @param string $time
     *
     * @return string
     */
    protected function getCorrectTimestamp($time)
    {
        // Get our timestamp (tricky!)
        bcscale(0);

        $time = number_format($time, 0, '.', ''); // save microtime to string
        $time = bcadd($time, '11644473600000'); // add epoch time
        $time = bcmul($time, 10000); // tenths of a microsecond.

        $binary = $this->si2bin($time, 64); // create 64 bit binary string
        $timestamp = '';
        for ($i = 0; $i < 8; $i++) {
            $timestamp .= chr(bindec(substr($binary, -(($i + 1) * 8), 8)));
        }

        return $timestamp;
    }

    /**
     * Create LMv2 response.
     *
     * @param string $password
     * @param string $username
     * @param string $domain
     * @param string $challenge NTLM Challenge
     * @param string $client    Random string
     *
     * @return string
     */
    protected function createLMv2Password($password, $username, $domain, $challenge, $client)
    {
        $lmPass = '00'; // by default 00
        // if $password > 15 than we can't use this method
        if (strlen($password) <= 15) {
            $ntlmHash = $this->md4Encrypt($password);
            $ntml2Hash = $this->md5Encrypt($ntlmHash, $this->convertTo16bit(strtoupper($username).$domain));

            $lmPass = bin2hex($this->md5Encrypt($ntml2Hash, $challenge.$client).$client);
        }

        return $this->createByte($lmPass, 24);
    }

    /**
     * Create NTLMv2 response.
     *
     * @param string $password
     * @param string $username
     * @param string $domain
     * @param string $challenge  Hex values
     * @param string $targetInfo Hex values
     * @param string $timestamp
     * @param string $client     Random bytes
     *
     * @return string
     *
     * @see http://davenport.sourceforge.net/ntlm.html#theNtlmResponse
     */
    protected function createNTLMv2Hash($password, $username, $domain, $challenge, $targetInfo, $timestamp, $client)
    {
        $ntlmHash = $this->md4Encrypt($password);
        $ntml2Hash = $this->md5Encrypt($ntlmHash, $this->convertTo16bit(strtoupper($username).$domain));

        // create blob
        $blob = $this->createBlob($timestamp, $client, $targetInfo);

        $ntlmv2Response = $this->md5Encrypt($ntml2Hash, $challenge.$blob);

        return $ntlmv2Response.$blob;
    }

    protected function createDesKey($key)
    {
        $material = array(bin2hex($key[0]));
        $len = strlen($key);
        for ($i = 1; $i < $len; $i++) {
            list($high, $low) = str_split(bin2hex($key[$i]));
            $v = $this->castToByte(ord($key[$i - 1]) << (7 + 1 - $i) | $this->uRShift(hexdec(dechex(hexdec($high) & 0xf).dechex(hexdec($low) & 0xf)), $i));
            $material[] = str_pad(substr(dechex($v), -2), 2, '0', STR_PAD_LEFT); // cast to byte
        }
        $material[] = str_pad(substr(dechex($this->castToByte(ord($key[6]) << 1)), -2), 2, '0');

        // odd parity
        foreach ($material as $k => $v) {
            $b = $this->castToByte(hexdec($v));
            $needsParity = (($this->uRShift($b, 7) ^ $this->uRShift($b, 6) ^ $this->uRShift($b, 5)
                        ^ $this->uRShift($b, 4) ^ $this->uRShift($b, 3) ^ $this->uRShift($b, 2)
                        ^ $this->uRShift($b, 1)) & 0x01) == 0;

            list($high, $low) = str_split($v);
            if ($needsParity) {
                $material[$k] = dechex(hexdec($high) | 0x0).dechex(hexdec($low) | 0x1);
            } else {
                $material[$k] = dechex(hexdec($high) & 0xf).dechex(hexdec($low) & 0xe);
            }
        }

        return $this->hex2bin(implode('', $material));
    }

    /** HELPER FUNCTIONS */
    /**
     * Create our security buffer depending on length and offset.
     *
     * @param string $value  Value we want to put in
     * @param int    $offset start of value
     * @param bool   $is16   Do we 16bit string or not?
     *
     * @return string
     */
    protected function createSecurityBuffer($value, $offset, $is16 = false)
    {
        $length = strlen(bin2hex($value));
        $length = $is16 ? $length / 2 : $length;
        $length = $this->createByte(str_pad(dechex($length), 2, '0', STR_PAD_LEFT), 2);

        return $length.$length.$this->createByte(dechex($offset), 4);
    }

    /**
     * Read our security buffer to fetch length and offset of our value.
     *
     * @param string $value Securitybuffer in hex
     *
     * @return array array with length and offset
     */
    protected function readSecurityBuffer($value)
    {
        $length = floor(hexdec(substr($value, 0, 4)) / 256) * 2;
        $offset = floor(hexdec(substr($value, 8, 4)) / 256) * 2;

        return array($length, $offset);
    }

    /**
     * Cast to byte java equivalent to (byte).
     *
     * @param int $v
     *
     * @return int
     */
    protected function castToByte($v)
    {
        return (($v + 128) % 256) - 128;
    }

    /**
     * Java unsigned right bitwise
     * $a >>> $b.
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    protected function uRShift($a, $b)
    {
        if ($b == 0) {
            return $a;
        }

        return ($a >> $b) & ~(1 << (8 * PHP_INT_SIZE - 1) >> ($b - 1));
    }

    /**
     * Right padding with 0 to certain length.
     *
     * @param string $input
     * @param int    $bytes Length of bytes
     * @param bool   $isHex Did we provided hex value
     *
     * @return string
     */
    protected function createByte($input, $bytes = 4, $isHex = true)
    {
        if ($isHex) {
            $byte = $this->hex2bin(str_pad($input, $bytes * 2, '00'));
        } else {
            $byte = str_pad($input, $bytes, "\x00");
        }

        return $byte;
    }

    /**
     * Create random bytes.
     *
     * @param $length
     *
     * @return string
     */
    protected function getRandomBytes($length)
    {
        $bytes = openssl_random_pseudo_bytes($length, $strong);

        if (false !== $bytes && true === $strong) {
            return $bytes;
        }

        throw new RuntimeException('OpenSSL did not produce a secure random number.');
    }

    /** ENCRYPTION ALGORITHMS */
    /**
     * DES Encryption.
     *
     * @param string $value
     * @param string $key
     *
     * @return string
     */
    protected function desEncrypt($value, $key)
    {
        $cipher = mcrypt_module_open(MCRYPT_DES, '', 'ecb', '');
        mcrypt_generic_init($cipher, $key, mcrypt_create_iv(mcrypt_enc_get_iv_size($cipher), MCRYPT_DEV_RANDOM));

        return mcrypt_generic($cipher, $value);
    }

    /**
     * MD5 Encryption.
     *
     * @param string $key Encryption key
     * @param string $msg Message to encrypt
     *
     * @return string
     */
    protected function md5Encrypt($key, $msg)
    {
        $blocksize = 64;
        if (strlen($key) > $blocksize) {
            $key = pack('H*', md5($key));
        }

        $key = str_pad($key, $blocksize, "\0");
        $ipadk = $key ^ str_repeat("\x36", $blocksize);
        $opadk = $key ^ str_repeat("\x5c", $blocksize);

        return pack('H*', md5($opadk.pack('H*', md5($ipadk.$msg))));
    }

    /**
     * MD4 Encryption.
     *
     * @param string $input
     *
     * @return string
     *
     * @see http://php.net/manual/en/ref.hash.php
     */
    protected function md4Encrypt($input)
    {
        $input = $this->convertTo16bit($input);

        return function_exists('hash') ? $this->hex2bin(hash('md4', $input)) : mhash(MHASH_MD4, $input);
    }

    /**
     * Convert UTF-8 to UTF-16.
     *
     * @param string $input
     *
     * @return string
     */
    protected function convertTo16bit($input)
    {
        return iconv('UTF-8', 'UTF-16LE', $input);
    }

    /**
     * Hex2bin replacement for < PHP 5.4.
     *
     * @param string $hex
     *
     * @return string Binary
     */
    protected function hex2bin($hex)
    {
        if (function_exists('hex2bin')) {
            return hex2bin($hex);
        } else {
            return pack('H*', $hex);
        }
    }

    /**
     * @param string $message
     */
    protected function debug($message)
    {
        $message = bin2hex($message);
        $messageId = substr($message, 16, 8);
        echo substr($message, 0, 16)." NTLMSSP Signature<br />\n";
        echo $messageId." Type Indicator<br />\n";

        if ($messageId == '02000000') {
            $map = array(
                'Challenge',
                'Context',
                'Target Information Security Buffer',
                'Target Name Data',
                'NetBIOS Domain Name',
                'NetBIOS Server Name',
                'DNS Domain Name',
                'DNS Server Name',
                'BLOB',
                'Target Information Terminator',
            );

            $data = $this->parseMessage2($this->hex2bin($message));

            foreach ($map as $key => $value) {
                echo bin2hex($data[$key]).' - '.$data[$key].' ||| '.$value."<br />\n";
            }
        } elseif ($messageId == '03000000') {
            $i = 0;
            $data[$i++] = substr($message, 24, 16);
            list($lmLength, $lmOffset) = $this->readSecurityBuffer($data[$i - 1]);

            $data[$i++] = substr($message, 40, 16);
            list($ntmlLength, $ntmlOffset) = $this->readSecurityBuffer($data[$i - 1]);

            $data[$i++] = substr($message, 56, 16);
            list($targetLength, $targetOffset) = $this->readSecurityBuffer($data[$i - 1]);

            $data[$i++] = substr($message, 72, 16);
            list($userLength, $userOffset) = $this->readSecurityBuffer($data[$i - 1]);

            $data[$i++] = substr($message, 88, 16);
            list($workLength, $workOffset) = $this->readSecurityBuffer($data[$i - 1]);

            $data[$i++] = substr($message, 104, 16);
            $data[$i++] = substr($message, 120, 8);
            $data[$i++] = substr($message, $targetOffset, $targetLength);
            $data[$i++] = substr($message, $userOffset, $userLength);
            $data[$i++] = substr($message, $workOffset, $workLength);
            $data[$i++] = substr($message, $lmOffset, $lmLength);
            $data[$i] = substr($message, $ntmlOffset, $ntmlLength);

            $map = array(
                'LM Response Security Buffer',
                'NTLM Response Security Buffer',
                'Target Name Security Buffer',
                'User Name Security Buffer',
                'Workstation Name Security Buffer',
                'Session Key Security Buffer',
                'Flags',
                'Target Name Data',
                'User Name Data',
                'Workstation Name Data',
                'LM Response Data',
                'NTLM Response Data',
            );

            foreach ($map as $key => $value) {
                echo $data[$key].' - '.$this->hex2bin($data[$key]).' ||| '.$value."<br />\n";
            }
        }

        echo '<br /><br />';
    }
}
