<?php

class PHPParser_Tests_TemplateLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testLoadWithoutSuffix() {
        $templateLoader = new PHPParser_TemplateLoader(
            new PHPParser_Parser(new PHPParser_Lexer),
            dirname(__FILE__)
        );

        // load this file as a template, as we don't really care about the contents
        $template = $templateLoader->load('TemplateLoaderTest.php');
        $this->assertInstanceOf('PHPParser_Template', $template);
    }

    public function testLoadWithSuffix() {
        $templateLoader = new PHPParser_TemplateLoader(
            new PHPParser_Parser(new PHPParser_Lexer),
            dirname(__FILE__), '.php'
        );

        // load this file as a template, as we don't really care about the contents
        $template = $templateLoader->load('TemplateLoaderTest');
        $this->assertInstanceOf('PHPParser_Template', $template);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNonexistentBaseDirectoryError() {
        new PHPParser_TemplateLoader(
            new PHPParser_Parser(new PHPParser_Lexer),
            dirname(__FILE__) . '/someDirectoryThatDoesNotExist'
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNonexistentFileError() {
        $templateLoader = new PHPParser_TemplateLoader(
            new PHPParser_Parser(new PHPParser_Lexer),
            dirname(__FILE__)
        );

        $templateLoader->load('SomeTemplateThatDoesNotExist');
    }
}