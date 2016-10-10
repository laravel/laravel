<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DomCrawler\Tests;

use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DomCrawler\FormFieldRegistry;
use Symfony\Component\DomCrawler\Field;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        // Ensure that the private helper class FormFieldRegistry is loaded
        class_exists('Symfony\\Component\\DomCrawler\\Form');
    }

    public function testConstructorThrowsExceptionIfTheNodeHasNoFormAncestor()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('
            <html>
                <input type="submit" />
                <form>
                    <input type="foo" />
                </form>
                <button />
            </html>
        ');

        $nodes = $dom->getElementsByTagName('input');

        try {
            $form = new Form($nodes->item(0), 'http://example.com');
            $this->fail('__construct() throws a \\LogicException if the node has no form ancestor');
        } catch (\LogicException $e) {
            $this->assertTrue(true, '__construct() throws a \\LogicException if the node has no form ancestor');
        }

        try {
            $form = new Form($nodes->item(1), 'http://example.com');
            $this->fail('__construct() throws a \\LogicException if the input type is not submit, button, or image');
        } catch (\LogicException $e) {
            $this->assertTrue(true, '__construct() throws a \\LogicException if the input type is not submit, button, or image');
        }

        $nodes = $dom->getElementsByTagName('button');

        try {
            $form = new Form($nodes->item(0), 'http://example.com');
            $this->fail('__construct() throws a \\LogicException if the node has no form ancestor');
        } catch (\LogicException $e) {
            $this->assertTrue(true, '__construct() throws a \\LogicException if the node has no form ancestor');
        }
    }

    /**
     * __construct() should throw \\LogicException if the form attribute is invalid
     * @expectedException \LogicException
     */
    public function testConstructorThrowsExceptionIfNoRelatedForm()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('
            <html>
                <form id="bar">
                    <input type="submit" form="nonexistent" />
                </form>
                <input type="text" form="nonexistent" />
                <button />
            </html>
        ');

        $nodes = $dom->getElementsByTagName('input');

        $form = new Form($nodes->item(0), 'http://example.com');
        $form = new Form($nodes->item(1), 'http://example.com');
    }

    public function testConstructorLoadsOnlyFieldsOfTheRightForm()
    {
        $dom = $this->createTestMultipleForm();

        $nodes = $dom->getElementsByTagName('form');
        $buttonElements = $dom->getElementsByTagName('button');

        $form = new Form($nodes->item(0), 'http://example.com');
        $this->assertCount(3, $form->all());

        $form = new Form($buttonElements->item(1), 'http://example.com');
        $this->assertCount(5, $form->all());
    }

    public function testConstructorHandlesFormAttribute()
    {
        $dom = $this->createTestHtml5Form();

        $inputElements = $dom->getElementsByTagName('input');
        $buttonElements = $dom->getElementsByTagName('button');

        // Tests if submit buttons are correctly assigned to forms
        $form1 = new Form($buttonElements->item(1), 'http://example.com');
        $this->assertSame($dom->getElementsByTagName('form')->item(0), $form1->getFormNode(), 'HTML5-compliant form attribute handled incorrectly');

        $form1 = new Form($inputElements->item(3), 'http://example.com');
        $this->assertSame($dom->getElementsByTagName('form')->item(0), $form1->getFormNode(), 'HTML5-compliant form attribute handled incorrectly');

        $form2 = new Form($buttonElements->item(0), 'http://example.com');
        $this->assertSame($dom->getElementsByTagName('form')->item(1), $form2->getFormNode(), 'HTML5-compliant form attribute handled incorrectly');
    }

    public function testConstructorHandlesFormValues()
    {
        $dom = $this->createTestHtml5Form();

        $inputElements = $dom->getElementsByTagName('input');
        $buttonElements = $dom->getElementsByTagName('button');

        $form1 = new Form($inputElements->item(3), 'http://example.com');
        $form2 = new Form($buttonElements->item(0), 'http://example.com');

        // Tests if form values are correctly assigned to forms
        $values1 = array(
            'apples' => array('1', '2'),
            'form_name' => 'form-1',
            'button_1' => 'Capture fields',
            'outer_field' => 'success',
        );
        $values2 = array(
            'oranges' => array('1', '2', '3'),
            'form_name' => 'form_2',
            'button_2' => '',
            'app_frontend_form_type_contact_form_type' => array('contactType' => '', 'firstName' => 'John'),
        );

        $this->assertEquals($values1, $form1->getPhpValues(), 'HTML5-compliant form attribute handled incorrectly');
        $this->assertEquals($values2, $form2->getPhpValues(), 'HTML5-compliant form attribute handled incorrectly');
    }

    public function testMultiValuedFields()
    {
        $form = $this->createForm('<form>
            <input type="text" name="foo[4]" value="foo" disabled="disabled" />
            <input type="text" name="foo" value="foo" disabled="disabled" />
            <input type="text" name="foo[2]" value="foo" disabled="disabled" />
            <input type="text" name="foo[]" value="foo" disabled="disabled" />
            <input type="text" name="bar[foo][]" value="foo" disabled="disabled" />
            <input type="text" name="bar[foo][foobar]" value="foo" disabled="disabled" />
            <input type="submit" />
        </form>
        ');

        $this->assertEquals(
            array_keys($form->all()),
            array('foo[2]', 'foo[3]', 'bar[foo][0]', 'bar[foo][foobar]')
        );

        $this->assertEquals($form->get('foo[2]')->getValue(), 'foo');
        $this->assertEquals($form->get('foo[3]')->getValue(), 'foo');
        $this->assertEquals($form->get('bar[foo][0]')->getValue(), 'foo');
        $this->assertEquals($form->get('bar[foo][foobar]')->getValue(), 'foo');

        $form['foo[2]'] = 'bar';
        $form['foo[3]'] = 'bar';

        $this->assertEquals($form->get('foo[2]')->getValue(), 'bar');
        $this->assertEquals($form->get('foo[3]')->getValue(), 'bar');

        $form['bar'] = array('foo' => array('0' => 'bar', 'foobar' => 'foobar'));

        $this->assertEquals($form->get('bar[foo][0]')->getValue(), 'bar');
        $this->assertEquals($form->get('bar[foo][foobar]')->getValue(), 'foobar');
    }

    /**
     * @dataProvider provideInitializeValues
     */
    public function testConstructor($message, $form, $values)
    {
        $form = $this->createForm('<form>'.$form.'</form>');
        $this->assertEquals(
            $values,
            array_map(function ($field) {
                    $class = get_class($field);

                    return array(substr($class, strrpos($class, '\\') + 1), $field->getValue());
                },
                $form->all()
            ),
            '->getDefaultValues() '.$message
        );
    }

    public function provideInitializeValues()
    {
        return array(
            array(
                'does not take into account input fields without a name attribute',
                '<input type="text" value="foo" />
                 <input type="submit" />',
                array(),
            ),
            array(
                'does not take into account input fields with an empty name attribute value',
                '<input type="text" name="" value="foo" />
                 <input type="submit" />',
                array(),
            ),
            array(
                'takes into account disabled input fields',
                '<input type="text" name="foo" value="foo" disabled="disabled" />
                 <input type="submit" />',
                array('foo' => array('InputFormField', 'foo')),
            ),
            array(
                'appends the submitted button value',
                '<input type="submit" name="bar" value="bar" />',
                array('bar' => array('InputFormField', 'bar')),
            ),
            array(
                'appends the submitted button value for Button element',
                '<button type="submit" name="bar" value="bar">Bar</button>',
                array('bar' => array('InputFormField', 'bar')),
            ),
            array(
                'appends the submitted button value but not other submit buttons',
                '<input type="submit" name="bar" value="bar" />
                 <input type="submit" name="foobar" value="foobar" />',
                 array('foobar' => array('InputFormField', 'foobar')),
            ),
            array(
                'turns an image input into x and y fields',
                '<input type="image" name="bar" />',
                array('bar.x' => array('InputFormField', '0'), 'bar.y' => array('InputFormField', '0')),
            ),
            array(
                'returns textareas',
                '<textarea name="foo">foo</textarea>
                 <input type="submit" />',
                 array('foo' => array('TextareaFormField', 'foo')),
            ),
            array(
                'returns inputs',
                '<input type="text" name="foo" value="foo" />
                 <input type="submit" />',
                 array('foo' => array('InputFormField', 'foo')),
            ),
            array(
                'returns checkboxes',
                '<input type="checkbox" name="foo" value="foo" checked="checked" />
                 <input type="submit" />',
                 array('foo' => array('ChoiceFormField', 'foo')),
            ),
            array(
                'returns not-checked checkboxes',
                '<input type="checkbox" name="foo" value="foo" />
                 <input type="submit" />',
                 array('foo' => array('ChoiceFormField', false)),
            ),
            array(
                'returns radio buttons',
                '<input type="radio" name="foo" value="foo" />
                 <input type="radio" name="foo" value="bar" checked="bar" />
                 <input type="submit" />',
                 array('foo' => array('ChoiceFormField', 'bar')),
            ),
            array(
                'returns file inputs',
                '<input type="file" name="foo" />
                 <input type="submit" />',
                 array('foo' => array('FileFormField', array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0))),
            ),
        );
    }

    public function testGetFormNode()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('<html><form><input type="submit" /></form></html>');

        $form = new Form($dom->getElementsByTagName('input')->item(0), 'http://example.com');

        $this->assertSame($dom->getElementsByTagName('form')->item(0), $form->getFormNode(), '->getFormNode() returns the form node associated with this form');
    }

    public function testGetFormNodeFromNamedForm()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('<html><form name="my_form"><input type="submit" /></form></html>');

        $form = new Form($dom->getElementsByTagName('form')->item(0), 'http://example.com');

        $this->assertSame($dom->getElementsByTagName('form')->item(0), $form->getFormNode(), '->getFormNode() returns the form node associated with this form');
    }

    public function testGetMethod()
    {
        $form = $this->createForm('<form><input type="submit" /></form>');
        $this->assertEquals('GET', $form->getMethod(), '->getMethod() returns get if no method is defined');

        $form = $this->createForm('<form method="post"><input type="submit" /></form>');
        $this->assertEquals('POST', $form->getMethod(), '->getMethod() returns the method attribute value of the form');

        $form = $this->createForm('<form method="post"><input type="submit" /></form>', 'put');
        $this->assertEquals('PUT', $form->getMethod(), '->getMethod() returns the method defined in the constructor if provided');

        $form = $this->createForm('<form method="post"><input type="submit" /></form>', 'delete');
        $this->assertEquals('DELETE', $form->getMethod(), '->getMethod() returns the method defined in the constructor if provided');

        $form = $this->createForm('<form method="post"><input type="submit" /></form>', 'patch');
        $this->assertEquals('PATCH', $form->getMethod(), '->getMethod() returns the method defined in the constructor if provided');
    }

    public function testGetSetValue()
    {
        $form = $this->createForm('<form><input type="text" name="foo" value="foo" /><input type="submit" /></form>');

        $this->assertEquals('foo', $form['foo']->getValue(), '->offsetGet() returns the value of a form field');

        $form['foo'] = 'bar';

        $this->assertEquals('bar', $form['foo']->getValue(), '->offsetSet() changes the value of a form field');

        try {
            $form['foobar'] = 'bar';
            $this->fail('->offsetSet() throws an \InvalidArgumentException exception if the field does not exist');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->offsetSet() throws an \InvalidArgumentException exception if the field does not exist');
        }

        try {
            $form['foobar'];
            $this->fail('->offsetSet() throws an \InvalidArgumentException exception if the field does not exist');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->offsetSet() throws an \InvalidArgumentException exception if the field does not exist');
        }
    }

    public function testSetValueOnMultiValuedFieldsWithMalformedName()
    {
        $form = $this->createForm('<form><input type="text" name="foo[bar]" value="bar" /><input type="text" name="foo[baz]" value="baz" /><input type="submit" /></form>');

        try {
            $form['foo[bar'] = 'bar';
            $this->fail('->offsetSet() throws an \InvalidArgumentException exception if the name is malformed.');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->offsetSet() throws an \InvalidArgumentException exception if the name is malformed.');
        }
    }

    public function testDisableValidation()
    {
        $form = $this->createForm('<form>
            <select name="foo[bar]">
                <option value="bar">bar</option>
            </select>
            <select name="foo[baz]">
                <option value="foo">foo</option>
            </select>
            <input type="submit" />
        </form>');

        $form->disableValidation();

        $form['foo[bar]']->select('foo');
        $form['foo[baz]']->select('bar');
        $this->assertEquals('foo', $form['foo[bar]']->getValue(), '->disableValidation() disables validation of all ChoiceFormField.');
        $this->assertEquals('bar', $form['foo[baz]']->getValue(), '->disableValidation() disables validation of all ChoiceFormField.');
    }

    public function testOffsetUnset()
    {
        $form = $this->createForm('<form><input type="text" name="foo" value="foo" /><input type="submit" /></form>');
        unset($form['foo']);
        $this->assertFalse(isset($form['foo']), '->offsetUnset() removes a field');
    }

    public function testOffsetExists()
    {
        $form = $this->createForm('<form><input type="text" name="foo" value="foo" /><input type="submit" /></form>');

        $this->assertTrue(isset($form['foo']), '->offsetExists() return true if the field exists');
        $this->assertFalse(isset($form['bar']), '->offsetExists() return false if the field does not exist');
    }

    public function testGetValues()
    {
        $form = $this->createForm('<form><input type="text" name="foo[bar]" value="foo" /><input type="text" name="bar" value="bar" /><select multiple="multiple" name="baz[]"></select><input type="submit" /></form>');
        $this->assertEquals(array('foo[bar]' => 'foo', 'bar' => 'bar', 'baz' => array()), $form->getValues(), '->getValues() returns all form field values');

        $form = $this->createForm('<form><input type="checkbox" name="foo" value="foo" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('bar' => 'bar'), $form->getValues(), '->getValues() does not include not-checked checkboxes');

        $form = $this->createForm('<form><input type="file" name="foo" value="foo" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('bar' => 'bar'), $form->getValues(), '->getValues() does not include file input fields');

        $form = $this->createForm('<form><input type="text" name="foo" value="foo" disabled="disabled" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('bar' => 'bar'), $form->getValues(), '->getValues() does not include disabled fields');
    }

    public function testSetValues()
    {
        $form = $this->createForm('<form><input type="checkbox" name="foo" value="foo" checked="checked" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $form->setValues(array('foo' => false, 'bar' => 'foo'));
        $this->assertEquals(array('bar' => 'foo'), $form->getValues(), '->setValues() sets the values of fields');
    }

    public function testMultiselectSetValues()
    {
        $form = $this->createForm('<form><select multiple="multiple" name="multi"><option value="foo">foo</option><option value="bar">bar</option></select><input type="submit" /></form>');
        $form->setValues(array('multi' => array("foo", "bar")));
        $this->assertEquals(array('multi' => array('foo', 'bar')), $form->getValues(), '->setValue() sets the values of select');
    }

    public function testGetPhpValues()
    {
        $form = $this->createForm('<form><input type="text" name="foo[bar]" value="foo" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('foo' => array('bar' => 'foo'), 'bar' => 'bar'), $form->getPhpValues(), '->getPhpValues() converts keys with [] to arrays');

        $form = $this->createForm('<form><input type="text" name="fo.o[ba.r]" value="foo" /><input type="text" name="ba r" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('fo.o' => array('ba.r' => 'foo'), 'ba r' => 'bar'), $form->getPhpValues(), '->getPhpValues() preserves periods and spaces in names');

        $form = $this->createForm('<form><input type="text" name="fo.o[ba.r][]" value="foo" /><input type="text" name="fo.o[ba.r][ba.z]" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('fo.o' => array('ba.r' => array('foo', 'ba.z' => 'bar'))), $form->getPhpValues(), '->getPhpValues() preserves periods and spaces in names recursively');

        $form = $this->createForm('<form><input type="text" name="foo[bar]" value="foo" /><input type="text" name="bar" value="bar" /><select multiple="multiple" name="baz[]"></select><input type="submit" /></form>');
        $this->assertEquals(array('foo' => array('bar' => 'foo'), 'bar' => 'bar'), $form->getPhpValues(), "->getPhpValues() doesn't return empty values");
    }

    public function testGetFiles()
    {
        $form = $this->createForm('<form><input type="file" name="foo[bar]" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array(), $form->getFiles(), '->getFiles() returns an empty array if method is get');

        $form = $this->createForm('<form method="post"><input type="file" name="foo[bar]" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('foo[bar]' => array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0)), $form->getFiles(), '->getFiles() only returns file fields for POST');

        $form = $this->createForm('<form method="post"><input type="file" name="foo[bar]" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>', 'put');
        $this->assertEquals(array('foo[bar]' => array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0)), $form->getFiles(), '->getFiles() only returns file fields for PUT');

        $form = $this->createForm('<form method="post"><input type="file" name="foo[bar]" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>', 'delete');
        $this->assertEquals(array('foo[bar]' => array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0)), $form->getFiles(), '->getFiles() only returns file fields for DELETE');

        $form = $this->createForm('<form method="post"><input type="file" name="foo[bar]" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>', 'patch');
        $this->assertEquals(array('foo[bar]' => array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0)), $form->getFiles(), '->getFiles() only returns file fields for PATCH');

        $form = $this->createForm('<form method="post"><input type="file" name="foo[bar]" disabled="disabled" /><input type="submit" /></form>');
        $this->assertEquals(array(), $form->getFiles(), '->getFiles() does not include disabled file fields');
    }

    public function testGetPhpFiles()
    {
        $form = $this->createForm('<form method="post"><input type="file" name="foo[bar]" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('foo' => array('bar' => array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0))), $form->getPhpFiles(), '->getPhpFiles() converts keys with [] to arrays');

        $form = $this->createForm('<form method="post"><input type="file" name="f.o o[bar]" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('f.o o' => array('bar' => array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0))), $form->getPhpFiles(), '->getPhpFiles() preserves periods and spaces in names');

        $form = $this->createForm('<form method="post"><input type="file" name="f.o o[bar][ba.z]" /><input type="file" name="f.o o[bar][]" /><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $this->assertEquals(array('f.o o' => array('bar' => array('ba.z' => array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0), array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0)))), $form->getPhpFiles(), '->getPhpFiles() preserves periods and spaces in names recursively');
    }

    /**
     * @dataProvider provideGetUriValues
     */
    public function testGetUri($message, $form, $values, $uri, $method = null)
    {
        $form = $this->createForm($form, $method);
        $form->setValues($values);

        $this->assertEquals('http://example.com'.$uri, $form->getUri(), '->getUri() '.$message);
    }

    public function testGetBaseUri()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('<form method="post" action="foo.php"><input type="text" name="bar" value="bar" /><input type="submit" /></form>');

        $nodes = $dom->getElementsByTagName('input');
        $form = new Form($nodes->item($nodes->length - 1), 'http://www.foo.com/');
        $this->assertEquals('http://www.foo.com/foo.php', $form->getUri());
    }

    public function testGetUriWithAnchor()
    {
        $form = $this->createForm('<form action="#foo"><input type="submit" /></form>', null, 'http://example.com/id/123');

        $this->assertEquals('http://example.com/id/123#foo', $form->getUri());
    }

    public function testGetUriActionAbsolute()
    {
        $formHtml = '<form id="login_form" action="https://login.foo.com/login.php?login_attempt=1" method="POST"><input type="text" name="foo" value="foo" /><input type="submit" /></form>';

        $form = $this->createForm($formHtml);
        $this->assertEquals('https://login.foo.com/login.php?login_attempt=1', $form->getUri(), '->getUri() returns absolute URIs set in the action form');

        $form = $this->createForm($formHtml, null, 'https://login.foo.com');
        $this->assertEquals('https://login.foo.com/login.php?login_attempt=1', $form->getUri(), '->getUri() returns absolute URIs set in the action form');

        $form = $this->createForm($formHtml, null, 'https://login.foo.com/bar/');
        $this->assertEquals('https://login.foo.com/login.php?login_attempt=1', $form->getUri(), '->getUri() returns absolute URIs set in the action form');

        // The action URI haven't the same domain Host have an another domain as Host
        $form = $this->createForm($formHtml, null, 'https://www.foo.com');
        $this->assertEquals('https://login.foo.com/login.php?login_attempt=1', $form->getUri(), '->getUri() returns absolute URIs set in the action form');

        $form = $this->createForm($formHtml, null, 'https://www.foo.com/bar/');
        $this->assertEquals('https://login.foo.com/login.php?login_attempt=1', $form->getUri(), '->getUri() returns absolute URIs set in the action form');
    }

    public function testGetUriAbsolute()
    {
        $form = $this->createForm('<form action="foo"><input type="submit" /></form>', null, 'http://localhost/foo/');
        $this->assertEquals('http://localhost/foo/foo', $form->getUri(), '->getUri() returns absolute URIs');

        $form = $this->createForm('<form action="/foo"><input type="submit" /></form>', null, 'http://localhost/foo/');
        $this->assertEquals('http://localhost/foo', $form->getUri(), '->getUri() returns absolute URIs');
    }

    public function testGetUriWithOnlyQueryString()
    {
        $form = $this->createForm('<form action="?get=param"><input type="submit" /></form>', null, 'http://localhost/foo/bar');
        $this->assertEquals('http://localhost/foo/bar?get=param', $form->getUri(), '->getUri() returns absolute URIs only if the host has been defined in the constructor');
    }

    public function testGetUriWithoutAction()
    {
        $form = $this->createForm('<form><input type="submit" /></form>', null, 'http://localhost/foo/bar');
        $this->assertEquals('http://localhost/foo/bar', $form->getUri(), '->getUri() returns path if no action defined');
    }

    public function provideGetUriValues()
    {
        return array(
            array(
                'returns the URI of the form',
                '<form action="/foo"><input type="submit" /></form>',
                array(),
                '/foo',
            ),
            array(
                'appends the form values if the method is get',
                '<form action="/foo"><input type="text" name="foo" value="foo" /><input type="submit" /></form>',
                array(),
                '/foo?foo=foo',
            ),
            array(
                'appends the form values and merges the submitted values',
                '<form action="/foo"><input type="text" name="foo" value="foo" /><input type="submit" /></form>',
                array('foo' => 'bar'),
                '/foo?foo=bar',
            ),
            array(
                'does not append values if the method is post',
                '<form action="/foo" method="post"><input type="text" name="foo" value="foo" /><input type="submit" /></form>',
                array(),
                '/foo',
            ),
            array(
                'does not append values if the method is patch',
                '<form action="/foo" method="post"><input type="text" name="foo" value="foo" /><input type="submit" /></form>',
                array(),
                '/foo',
                'PUT',
            ),
            array(
                'does not append values if the method is delete',
                '<form action="/foo" method="post"><input type="text" name="foo" value="foo" /><input type="submit" /></form>',
                array(),
                '/foo',
                'DELETE',
            ),
            array(
                'does not append values if the method is put',
                '<form action="/foo" method="post"><input type="text" name="foo" value="foo" /><input type="submit" /></form>',
                array(),
                '/foo',
                'PATCH',
            ),
            array(
                'appends the form values to an existing query string',
                '<form action="/foo?bar=bar"><input type="text" name="foo" value="foo" /><input type="submit" /></form>',
                array(),
                '/foo?bar=bar&foo=foo',
            ),
            array(
                'returns an empty URI if the action is empty',
                '<form><input type="submit" /></form>',
                array(),
                '/',
            ),
            array(
                'appends the form values even if the action is empty',
                '<form><input type="text" name="foo" value="foo" /><input type="submit" /></form>',
                array(),
                '/?foo=foo',
            ),
            array(
                'chooses the path if the action attribute value is a sharp (#)',
                '<form action="#" method="post"><input type="text" name="foo" value="foo" /><input type="submit" /></form>',
                array(),
                '/#',
            ),
        );
    }

    public function testHas()
    {
        $form = $this->createForm('<form method="post"><input type="text" name="bar" value="bar" /><input type="submit" /></form>');

        $this->assertFalse($form->has('foo'), '->has() returns false if a field is not in the form');
        $this->assertTrue($form->has('bar'), '->has() returns true if a field is in the form');
    }

    public function testRemove()
    {
        $form = $this->createForm('<form method="post"><input type="text" name="bar" value="bar" /><input type="submit" /></form>');
        $form->remove('bar');
        $this->assertFalse($form->has('bar'), '->remove() removes a field');
    }

    public function testGet()
    {
        $form = $this->createForm('<form method="post"><input type="text" name="bar" value="bar" /><input type="submit" /></form>');

        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Field\\InputFormField', $form->get('bar'), '->get() returns the field object associated with the given name');

        try {
            $form->get('foo');
            $this->fail('->get() throws an \InvalidArgumentException if the field does not exist');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->get() throws an \InvalidArgumentException if the field does not exist');
        }
    }

    public function testAll()
    {
        $form = $this->createForm('<form method="post"><input type="text" name="bar" value="bar" /><input type="submit" /></form>');

        $fields = $form->all();
        $this->assertCount(1, $fields, '->all() return an array of form field objects');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Field\\InputFormField', $fields['bar'], '->all() return an array of form field objects');
    }

    public function testSubmitWithoutAFormButton()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('
            <html>
                <form>
                    <input type="foo" />
                </form>
            </html>
        ');

        $nodes = $dom->getElementsByTagName('form');
        $form = new Form($nodes->item(0), 'http://example.com');
        $this->assertSame($nodes->item(0), $form->getFormNode(), '->getFormNode() returns the form node associated with this form');
    }

    public function testTypeAttributeIsCaseInsensitive()
    {
        $form = $this->createForm('<form method="post"><input type="IMAGE" name="example" /></form>');
        $this->assertTrue($form->has('example.x'), '->has() returns true if the image input was correctly turned into an x and a y fields');
        $this->assertTrue($form->has('example.y'), '->has() returns true if the image input was correctly turned into an x and a y fields');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFormFieldRegistryAddThrowAnExceptionWhenTheNameIsMalformed()
    {
        $registry = new FormFieldRegistry();
        $registry->add($this->getFormFieldMock('[foo]'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFormFieldRegistryRemoveThrowAnExceptionWhenTheNameIsMalformed()
    {
        $registry = new FormFieldRegistry();
        $registry->remove('[foo]');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFormFieldRegistryGetThrowAnExceptionWhenTheNameIsMalformed()
    {
        $registry = new FormFieldRegistry();
        $registry->get('[foo]');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFormFieldRegistryGetThrowAnExceptionWhenTheFieldDoesNotExist()
    {
        $registry = new FormFieldRegistry();
        $registry->get('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFormFieldRegistrySetThrowAnExceptionWhenTheNameIsMalformed()
    {
        $registry = new FormFieldRegistry();
        $registry->set('[foo]', null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFormFieldRegistrySetThrowAnExceptionWhenTheFieldDoesNotExist()
    {
        $registry = new FormFieldRegistry();
        $registry->set('foo', null);
    }

    public function testFormFieldRegistryHasReturnsTrueWhenTheFQNExists()
    {
        $registry = new FormFieldRegistry();
        $registry->add($this->getFormFieldMock('foo[bar]'));

        $this->assertTrue($registry->has('foo'));
        $this->assertTrue($registry->has('foo[bar]'));
        $this->assertFalse($registry->has('bar'));
        $this->assertFalse($registry->has('foo[foo]'));
    }

    public function testFormRegistryFieldsCanBeRemoved()
    {
        $registry = new FormFieldRegistry();
        $registry->add($this->getFormFieldMock('foo'));
        $registry->remove('foo');
        $this->assertFalse($registry->has('foo'));
    }

    public function testFormRegistrySupportsMultivaluedFields()
    {
        $registry = new FormFieldRegistry();
        $registry->add($this->getFormFieldMock('foo[]'));
        $registry->add($this->getFormFieldMock('foo[]'));
        $registry->add($this->getFormFieldMock('bar[5]'));
        $registry->add($this->getFormFieldMock('bar[]'));
        $registry->add($this->getFormFieldMock('bar[baz]'));

        $this->assertEquals(
            array('foo[0]', 'foo[1]', 'bar[5]', 'bar[6]', 'bar[baz]'),
            array_keys($registry->all())
        );
    }

    public function testFormRegistrySetValues()
    {
        $registry = new FormFieldRegistry();
        $registry->add($f2 = $this->getFormFieldMock('foo[2]'));
        $registry->add($f3 = $this->getFormFieldMock('foo[3]'));
        $registry->add($fbb = $this->getFormFieldMock('foo[bar][baz]'));

        $f2
            ->expects($this->exactly(2))
            ->method('setValue')
            ->with(2)
        ;

        $f3
            ->expects($this->exactly(2))
            ->method('setValue')
            ->with(3)
        ;

        $fbb
            ->expects($this->exactly(2))
            ->method('setValue')
            ->with('fbb')
        ;

        $registry->set('foo[2]', 2);
        $registry->set('foo[3]', 3);
        $registry->set('foo[bar][baz]', 'fbb');

        $registry->set('foo', array(
            2     => 2,
            3     => 3,
            'bar' => array(
                'baz' => 'fbb',
             ),
        ));
    }

    public function testDifferentFieldTypesWithSameName()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('
            <html>
                <body>
                    <form action="/">
                        <input type="hidden" name="option" value="default">
                        <input type="radio" name="option" value="A">
                        <input type="radio" name="option" value="B">
                        <input type="hidden" name="settings[1]" value="0">
                        <input type="checkbox" name="settings[1]" value="1" id="setting-1">
                        <button>klickme</button>
                    </form>
                </body>
            </html>
        ');
        $form = new Form($dom->getElementsByTagName('form')->item(0), 'http://example.com');

        $this->assertInstanceOf('Symfony\Component\DomCrawler\Field\ChoiceFormField', $form->get('option'));
    }

    protected function getFormFieldMock($name, $value = null)
    {
        $field = $this
            ->getMockBuilder('Symfony\\Component\\DomCrawler\\Field\\FormField')
            ->setMethods(array('getName', 'getValue', 'setValue', 'initialize'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $field
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name))
        ;

        $field
            ->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($value))
        ;

        return $field;
    }

    protected function createForm($form, $method = null, $currentUri = null)
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('<html>'.$form.'</html>');

        $xPath = new \DOMXPath($dom);
        $nodes = $xPath->query('//input | //button');

        if (null === $currentUri) {
            $currentUri = 'http://example.com/';
        }

        return new Form($nodes->item($nodes->length - 1), $currentUri, $method);
    }

    protected function createTestHtml5Form()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('
        <html>
            <h1>Hello form</h1>
            <form id="form-1" action="" method="POST">
                <div><input type="checkbox" name="apples[]" value="1" checked /></div>
                <input form="form_2" type="checkbox" name="oranges[]" value="1" checked />
                <div><label></label><input form="form-1" type="hidden" name="form_name" value="form-1" /></div>
                <input form="form-1" type="submit" name="button_1" value="Capture fields" />
                <button form="form_2" type="submit" name="button_2">Submit form_2</button>
            </form>
            <input form="form-1" type="checkbox" name="apples[]" value="2" checked />
            <form id="form_2" action="" method="POST">
                <div><div><input type="checkbox" name="oranges[]" value="2" checked />
                <input type="checkbox" name="oranges[]" value="3" checked /></div></div>
                <input form="form_2" type="hidden" name="form_name" value="form_2" />
                <input form="form-1" type="hidden" name="outer_field" value="success" />
                <button form="form-1" type="submit" name="button_3">Submit from outside the form</button>
                <div>
                    <label for="app_frontend_form_type_contact_form_type_contactType">Message subject</label>
                    <div>
                        <select name="app_frontend_form_type_contact_form_type[contactType]" id="app_frontend_form_type_contact_form_type_contactType"><option selected="selected" value="">Please select subject</option><option id="1">Test type</option></select>
                    </div>
                </div>
                <div>
                    <label for="app_frontend_form_type_contact_form_type_firstName">Firstname</label>
                    <input type="text" name="app_frontend_form_type_contact_form_type[firstName]" value="John" id="app_frontend_form_type_contact_form_type_firstName"/>
                </div>
            </form>
            <button />
        </html>');

        return $dom;
    }

    protected function createTestMultipleForm()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('
        <html>
            <h1>Hello form</h1>
            <form action="" method="POST">
                <div><input type="checkbox" name="apples[]" value="1" checked /></div>
                <input type="checkbox" name="oranges[]" value="1" checked />
                <div><label></label><input type="hidden" name="form_name" value="form-1" /></div>
                <input type="submit" name="button_1" value="Capture fields" />
                <button type="submit" name="button_2">Submit form_2</button>
            </form>
            <form action="" method="POST">
                <div><div><input type="checkbox" name="oranges[]" value="2" checked />
                <input type="checkbox" name="oranges[]" value="3" checked /></div></div>
                <input type="hidden" name="form_name" value="form_2" />
                <input type="hidden" name="outer_field" value="success" />
                <button type="submit" name="button_3">Submit from outside the form</button>
            </form>
            <button />
        </html>');

        return $dom;
    }

    public function testgetPhpValuesWithEmptyTextarea()
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('
              <html>
                  <form>
                      <textarea name="example"></textarea>
                  </form>
              </html>
          ');

        $nodes = $dom->getElementsByTagName('form');
        $form = new Form($nodes->item(0), 'http://example.com');
        $this->assertEquals($form->getPhpValues(), array('example' => ''));
    }
}
