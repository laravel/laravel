<?php 

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used
	| by the validator class. Some of the rules contain multiple versions,
	| such as the size (max, min, between) rules. These versions are used
	| for different input types such as strings and files.
	|
	| These language lines may be easily changed to provide custom error
	| messages in your application. Error messages for custom validation
	| rules may also be added to this file.
	|
	*/

	"accepted"       => ":attribute πρέπει να αποδεχθεί.",
	"active_url"     => ":attribute δεν είναι έγκυρο URL.",
	"after"          => ":attribute πρέπει να είναι ημερομηνία μετά την :date.",
	"alpha"          => ":attribute μπορεί να περιέχει μόνο χαρακτήρες.",
	"alpha_dash"     => ":attribute μπορεί να περιέχει μόνο χαρακτήρες, αριθμούς, and παύλες.",
	"alpha_num"      => ":attribute μπορεί να περιέχει μόνο χαρακτήρεςκαι  αριθμούς.",
	"array"          => ":attribute πρέπει να έχει επιλεγή.",
	"before"         => ":attribute πρέπει να είναι ημερομηνία πριν την :date :date.",
	"between"        => array(
		"numeric" => ":attribute πρέπει να είναι :min - :max.",
		"file"    => ":attribute πρέπει να είναι :min - :max kilobytes.",
		"string"  => ":attribute πρέπει να είναι :min - :max χαρακτήρεσ.",
	),
	"confirmed"      => ":attribute: Η επιβεβαίωση δεν ταιρίαζει.",
	"count"          => ":attribute πρέπει να έχει ακριβώς :count επιλεγμένα στοιχεία.",
	"countbetween"   => ":attribute πρέπει να έχει :min - :max επιλεγμένα στοιχεία.",
	"countmax"       => ":attribute πρέπει να έχει λιγότερα απο :max επιλεγμένα στοιχεία.",
	"countmin"       => ":attribute πρέπει να έχει το λιγότερο :min επιλεγμένα στοιχεία.",
	"different"      => ":attribute και :other πρέπει να είναι διαφορετικά.",
	"email"          => ":attribute: Η μορφή δεν είναι σωστή.",
	"exists"         => ":attribute δεν είναι έγκυρο.",
	"image"          => ":attribute πρέπει να είναι εικόνα.",
	"in"             => "selected δεν είναι έγκυρο.",
	"integer"        => ":attribute πρέπει να είναι αριθμός.",
	"ip"             => ":attribute πρέπει να είναι έγκυρη διεύθυνση IP.",
	"match"          => ":attribute: Η μορφή δεν είναι σωστή.",
	"max"            => array(
		"numeric" => ":attribute πρέπει να είναι μικρότερο απο :max.",
		"file"    => ":attribute πρέπει να είναι μικρότερο απο :max kilobytes.",
		"string"  => ":attribute πρέπει να είναι μικρότερο απο :max χαρακτήρες.",
	),
	"mimes"          => ":attribute: Πρέπει να είναι αρχείο τύπου: :values.",
	"min"            => array(
		"numeric" => ":attribute: Το ελάχιστο είναι: :min.",
		"file"    => ":attribute πρέπει να έχει το λιγότερο :min kilobytes.",
		"string"  => ":attribute πρέπει να έχει το λιγότερο :min χαρακτήρες.",
	),
	"not_in"         => "Το επιλεγμένο :attribute δεν έιναι έγκυρο.",
	"numeric"        => ":attribute πρέπει να είναι αριθμός.",
	"required"       => ":attribute πρέπει να συμπληρωθεί.",
	"same"           => ":attribute και :other πρέπει να είναι ίδια.",
	"size"           => array(
		"numeric" => ":attribute πρέπει να είναι :size.",
		"file"    => ":attribute πρέπει να είναι :size kilobyte.",
		"string"  => ":attribute πρέπει να είναι :size χαρακτήρες.",
	),
	"unique"         => ":attribute χρησηομοποιήται ήδη.",
	"url"            => ":attribute: Η μορφή δεν είναι σωστή.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute_rule" to name the lines. This helps keep your
	| custom validation clean and tidy.
	|
	| So, say you want to use a custom validation message when validating that
	| the "email" attribute is unique. Just add "email_unique" to this array
	| with your custom message. The Validator will handle the rest!
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as "E-Mail Address" instead
	| of "email". Your users will thank you.
	|
	| The Validator class will automatically search this array of lines it
	| is attempting to replace the :attribute place-holder in messages.
	| It's pretty slick. We think you'll like it.
	|
	*/

	'attributes' => array(),

);