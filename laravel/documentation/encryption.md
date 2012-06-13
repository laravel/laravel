# Encryption

## Contents

- [The Basics](#the-basics)
- [Encrypting A String](#encrypt)
- [Decrypting A String](#decrypt)

<a name="the-basics"></a>
## The Basics

Laravel's **Crypter** class provides a simple interface for handling secure, two-way encryption. By default, the Crypter class provides strong AES-256 encryption and decryption out of the box via the Mcrypt PHP extension.

> **Note:** Don't forget to install the Mcrypt PHP extension on your server.

<a name="encrypt"></a>
## Encrypting A String

#### Encrypting a given string:

	$encrypted = Crypter::encrypt($value);

<a name="decrypt"></a>
## Decrypting A String

#### Decrypting a string:

	$decrypted = Crypter::decrypt($encrypted);

> **Note:** It's incredibly important to point out that the decrypt method will only decrypt strings that were encrypted using **your** application key.