# Changelog

## 0.3.9 - 2014-11-09

- PHP 5.6 improvements ([#482](https://github.com/phpseclib/phpseclib/pull/482), [#491](https://github.com/phpseclib/phpseclib/issues/491))

## 0.3.8 - 2014-09-12

- improve support for indef lengths in File_ASN1
- add hmac-sha2-256 support to Net_SSH2
- make it so negotiated algorithms can be seen before Net_SSH2 login
- add sha256-96 and sha512-96 to Crypt_Hash
- window size handling adjustments in Net_SSH2

## 0.3.7 - 2014-07-05

- auto-detect public vs private keys
- add file_exists, is_dir, is_file, readlink and symlink to Net_SFTP
- add support for recursive nlist and rawlist
- make it so nlist and rawlist can return pre-sorted output
- make it so callback functions can make exec() return early
- add signSPKAC and saveSPKAC methods to File_X509
- add support for PKCS8 keys in Crypt_RSA
- add pbkdf1 support to setPassword() in Crypt_Base
- add getWindowColumns, getWindowRows, setWindowColumns, setWindowRows to Net_SSH2
- add support for filenames with spaces in them to Net_SCP

## 0.3.6 - 2014-02-23

- add preliminary support for custom SSH subsystems
- add ssh-agent support

## 0.3.5 - 2013-07-11

- numerous SFTP changes:
 - chown
 - chgrp
 - truncate
 - improved file type detection
 - put() can write to te middle of a file
 - mkdir accepts the same paramters that PHP's mkdir does
 - the ability to upload/download 2GB files
- across-the-board speedups for the various encryption algorithms
- multi-factor authentication support for Net_SSH2
- a $callback parameter for Net_SSH2::exec
- new classes:
 - Net_SFTP_StreamWrapper
 - Net_SCP
 - Crypt_Twofish
 - Crypt_Blowfish

## 0.3.1 - 2012-11-20

- add Net_SSH2::enableQuietMode() for suppressing stderr
- add Crypt_RSA::__toString() and Crypt_RSA::getSize()
- fix problems with File_X509::validateDate(), File_X509::sign() and Crypt_RSA::verify()
- use OpenSSL to speed up modular exponention in Math_BigInteger
- improved timeout functionality in Net_SSH2
- add support for SFTPv2
- add support for CRLs in File_X509
- SSH-2.0-SSH doesn't implement hmac-*-96 correctly

## 0.3.0 - 2012-07-08

- add support for reuming Net_SFTP::put()
- add support for recursive deletes and recursive chmods to Net_SFTP
- add setTimeout() to Net_SSH2
- add support for PBKDF2 to the various Crypt_* classes via setPassword()
- add File_X509 and File_ASN1
- add the ability to decode various formats in Crypt_RSA
- make Net_SSH2::getServerPublicHostKey() return a printer-friendly version of the public key

## 0.2.2 - 2011-05-09

- CFB and OFB modes were added to all block ciphers
- support for interactive mode was added to Net_SSH2
- Net_SSH2 now has limited keyboard_interactive authentication support
- support was added for PuTTY formatted RSA private keys and XML formatted RSA private keys
- Crypt_RSA::loadKey() will now try all key types automatically
= add support for AES-128-CBC and DES-EDE3-CFB encrypted RSA private keys
- add Net_SFTP::stat(), Net_SFTP::lstat() and Net_SFTP::rawlist()
- logging was added to Net_SSH1
- the license was changed to the less restrictive MIT license