<?php 

/* Por Vedovelli (github.com/vedovelli) em 30/09/2012 */

return array(

	/*
	|--------------------------------------------------------------------------
	| Linhas de Idioma para Validação
	|--------------------------------------------------------------------------
	|
	| As seguintes linhas contém as mensagens padrão utilizadas pela classe
	| validator. Algumas das regras contém múltiplas versões, como por ex.
	| regras de tamanho (max, min, entre). Estas versões são utilizadas por
	| tipos diferentes de input, tais como strings e files.
	| 
	| Estas linhas de linguagem podem facilmente ser modificadas para prover
	| mensagens personalizadas para sua aplicação. Outras mensagens de erro
	| de validação também podem ser adicionadas a este arquivo.
	|
	*/

	"accepted"       => "O(A) :attribute precisa ser aceito(a).",
	"active_url"     => "A :attribute não é uma URL válida.",
	"after"          => "A :attribute precisa ser uma data anterior a :date.",
	"alpha"          => "O :attribute só pode conter letras.",
	"alpha_dash"     => "O :attribute só pode conter letras, números e hífen.",
	"alpha_num"      => "O :attribute só pode conter letras e números.",
	"array"          => "O :attribute precisa ter elementos selecionados.",
	"before"         => "A :attribute precisa ser uma data anterior a :date.",,
	"between"        => array(
		"numeric" => "O :attribute precisa estar entre :min - :max.",
		"file"    => "O :attribute precisa estar entre :min - :max kilobytes.",
		"string"  => "O :attribute  precisa estar entre :min - :max caracteres.",
	),
	"confirmed"      => "As :attribute não conferem.",
	"count"          => "O :attribute precisa ter exatamente :count elementos selecionados.",
	"countbetween"   => "O :attribute precisa ter :min and :max elementos selecionados.",
	"countmax"       => "O :attribute precisa ter menos do que :max elementos selecionados.",
	"countmin"       => "O :attribute precisa ter pelo menos :min elementos selecionados.",
	"different"      => "O :attribute e :other precisam ser diferentes.",
	"email"          => "O :attribute formato é inválido.",
	"exists"         => "O :attribute selecionado é inválido.",
	"image"          => "O :attribute precisa ser uma imagem.",
	"in"             => "O :attribute selecionado é inválido.",
	"integer"        => "O :attribute precisa ser um inteiro.",
	"ip"             => "O :attribute precisa ser um IP válido.",
	"match"          => "O :attribute formato é inválido.",
	"max"            => array(
		"numeric" => "O :attribute precisa ser menor do que :max.",
		"file"    => "O :attribute precisa ser menor do que :max kilobytes.",
		"string"  => "O :attribute precisa ter menos do que :max caracteres.",
	),
	"mimes"          => "O :attribute precisa ser um arquivo do tipo: :values.",
	"min"            => array(
		"numeric" => "O :attribute precisa ter pelo menos :min.",
		"file"    => "O :attribute precisa ter pelo menos :min kilobytes.",
		"string"  => "O :attribute precisa ter pelo menos :min caracteres.",
	),
	"not_in"         => "O :attribute selecionado é inválido.",
	"numeric"        => "O :attribute precisa ser um número.",
	"required"       => "O :attribute é de preenchimento obrigatório.",
	"same"           => "O :attribute e :other precisam ser iguais.",
	"size"           => array(
		"numeric" => "O :attribute precisa ter :size.",
		"file"    => "O :attribute precisa ter :size kilobyte.",
		"string"  => "O :attribute precisa ter :size caracteres.",
	),
	"unique"         => "O :attribute não está disponível.",
	"url"            => "O :attribute possui formato inválido.",

	/*
	|--------------------------------------------------------------------------
	| Linhas de Idioma Personalizadas para Validação
	|--------------------------------------------------------------------------
	| 
	| Aqui você pode adicionar mensagens de validação personalizadas para 
	| atributos utilizando a convenção "attribute_rule" para nomear as linhas.
	| O procedimento ajuda a manter sua validação limpa e organizada.
	| 
	| Então, vamos supor que você queira utilizar uma mensagem personalizada
	| para validar se o e-mail é único. Basta adicionar "email_unique" no
	| array abaixo com sua mensagem personalizada. A classe Validator fará
	| o restante do trabalho.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Atributos de Validação
	|--------------------------------------------------------------------------
	| 
	| As seguintes linhas de idioma são utilizadas para trocar place-holders
	| de atributo por algo mais legível, como por exemplo "Endereço de E-mail"
	| ao invés de simplesmente "email". Seus usuários agradecerão.
	| 
	| A classe Validator buscará neste array de linhas quando fazendo a substi-
	| tuição do :attribute nas mensagens. É bastante direto. Pensamos que você
	| gostará.
	| 
	*/

	'attributes' => array(),

);