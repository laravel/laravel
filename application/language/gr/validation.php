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

	"accepted"       => "Το πεδίο :attribute πρέπει να εγκριθεί.",
	"active_url"     => "Το πεδίο :attribute δεν ειναι σωστό URL.",
	"after"          => "Το πεδίο :attribute πρέπει η ημερομηνία να ειναι μετά :date.",
	"alpha"          => "Το πεδίο :attribute μπορεί να περιλαμβάνει μόνο γράμματα.",
	"alpha_dash"     => "Το πεδίο :attribute μπορεί να περιλαμβάνει μόνο γράμματα, αριθμούς και παύλες.",
	"alpha_num"      => "Το πεδίο :attribute μπορεί να περιλαμβάνει μόνο γράμματα και αριθμούς.",
	"array"          => "Το πεδίο :attribute πρέπει να περιλαμβάνει επιλεγμένα αντικείμενα.",
	"before"         => "Το πεδίο :attribute πρέπει η ημερομηνία να ειναι πριν από :date.",
	"between"        => array(
		"numeric" => "Το πεδίο :attribute πρέπει να έχει τιμές μεταξύ :min - :max.",
		"file"    => "Το πεδίο :attribute πρέπει να ειναι ανάμεσα σε :min - :max kb.",
		"string"  => "Το πεδίο :attribute πρέπει να περιλαμβάνει :min - :max χαρακτήρες.",
	),
	"confirmed"      => "Το πεδίο :attribute δεν πέρασε τον έλεγχο.",
	"count"          => "Το πεδίο :attribute πρέπει να έχει ακριβώς :count επιλεγμένα αντικείμενα.",
	"countbetween"   => "Το πεδίο :attribute πρέπει να είναι ανάμεσα σε :min και :max επιλεγμένα αντικείμενα.",
	"countmax"       => "Το πεδίο :attribute πρέπει να έχει λιγότερα από :max επιλεγμένα αντικείμενα.",
	"countmin"       => "Το πεδίο :attribute πρέπει να έχει τουλάχιστον :min επιλεγμένα αντικείμενα.",
	"different"      => "Τα πεδία :attribute και :other πρέπει να ειναι διαφορετικά.",
	"email"          => "Στο πεδίο :attribute η μορφοποίηση ειναι άκυρη.",
	"exists"         => "Το επιλεγμένο πεδίο :attribute είναι άκυρο.",
	"image"          => "Το πεδίο :attribute πρέπει να είναι εικόνα.",
	"in"             => "Το επιλεγμένο πεδίο :attribute είναι άκυρο.",
	"integer"        => "Το πεδίο :attribute πρέπει να ειναι αριθμός.",
	"ip"             => "Το πεδίο :attribute πρέπει να ειναι μια έγκυρη διεύθυνση IP.",
	"match"          => "Το φορμάτ του πεδίου :attribute είναι άκυρο.",
	"max"            => array(
		"numeric" => "Το πεδίο :attribute πρέπει να είναι μικρότερο από :max.",
		"file"    => "Το πεδίο :attribute πρέπει να είναι μικρότερο από :max kb.",
		"string"  => "Το πεδίο :attribute πρέπει να έχει λιγότερους από :max χαρακτήρες.",
	),
	"mimes"          => "Το πεδίο :attribute πρέπει να ειναι αρχείο με τύπο: :values.",
	"min"            => array(
		"numeric" => "Το πεδίο :attribute πρέπει να είναι τουλάχιστον :min.",
		"file"    => "Το πεδίο :attribute πρέπει να είναι μικρότερο από :max kb.",
		"string"  => "Το πεδίο :attribute πρέπει να έχει λιγότερους από :max χαρακτήρες.",
	),
	"not_in"         => "Το επιλεγμένο πεδίο :attribute είναι άκυρο.",
	"numeric"        => "Το πεδίο :attribute πρέπει να είναι αριθμός.",
	"required"       => "Το πεδίο :attribute είναι απαραίτητο.",
	"same"           => "Τα πεδία :attribute και :other πρέπει να είναι ίδια.",
	"size"           => array(
		"numeric" => "Το πεδίο :attribute πρέπει να ειναι :size.",
		"file"    => "Το πεδίο :attribute πρέπει να έχει μέγεθος :size kb.",
		"string"  => "Το πεδίο :attribute πρέπει να είναι :size χαρακτήρες.",
	),
	"unique"         => "Το πεδίο :attribute έχει ήδη ανατεθεί.",
	"url"            => "Το πεδίο :attribute είναι άκυρο.",

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