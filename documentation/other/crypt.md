## Encryption

- [The Basics](#basics)
- [Encrypting A String](#encrypt)
- [Decrypting A String](#decrypt)

<a name="basics"></a>
### The Basics

Need to do secure, two-way encryption? Laravel has you covered with the **Crypt** class. The Crypt class provides strong AES-256 encryption and decryption out of the box via the Mcrypt PHP extension.

To get started, you must set your **application key** in the **application/config/application.php** file. This key should be very random and very secret, as it will be used during the encryption and decryption process. It is best to use a random, 32 character alpha-numeric string:

	'key' => 'xXSAVghP7myRo5xqJAnMvQwBc7j8qBZI';

Wonderful. You're ready to start encrypting.

> **Note:** Don't forget to install the Mcrypt PHP extension on your server.

<a name="encrypt"></a>
### Encrypting A String

Encrypting a string is a breeze. Just pass it to the **encrypt** method on the Crypt class:

	Crypt::encrypt($value);

Do you feel like James Bond yet?

<a name="decrypt"></a>
### Decrypting A String

So you're ready to decrypt a string? It's simple. Just use the **decrypt** method on the Crypt class:

	Crypt::decrypt($encrypted_value);

> **Note:** The decrypt method will only decrypt strings that were encrypted using **your** application key.