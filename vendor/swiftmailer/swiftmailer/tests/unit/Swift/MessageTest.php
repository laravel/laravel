<?php

class Swift_MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testCloning()
    {
        $message1 = new Swift_Message('subj', 'body', 'ctype');
        $message2 = new Swift_Message('subj', 'body', 'ctype');
        $message1_clone = clone $message1;

        $this->_recursiveObjectCloningCheck($message1, $message2, $message1_clone);
    }

    public function testCloningWithSigners()
    {
        $message1 = new Swift_Message('subj', 'body', 'ctype');
        $signer = new Swift_Signers_DKIMSigner(dirname(dirname(__DIR__)).'/_samples/dkim/dkim.test.priv', 'test.example', 'example');
        $message1->attachSigner($signer);
        $message2 = new Swift_Message('subj', 'body', 'ctype');
        $signer = new Swift_Signers_DKIMSigner(dirname(dirname(__DIR__)).'/_samples/dkim/dkim.test.priv', 'test.example', 'example');
        $message2->attachSigner($signer);
        $message1_clone = clone $message1;

        $this->_recursiveObjectCloningCheck($message1, $message2, $message1_clone);
    }

    public function testBodySwap()
    {
        $message1 = new Swift_Message('Test');
        $html = Swift_MimePart::newInstance('<html></html>', 'text/html');
        $html->getHeaders()->addTextHeader('X-Test-Remove', 'Test-Value');
        $html->getHeaders()->addTextHeader('X-Test-Alter', 'Test-Value');
        $message1->attach($html);
        $source = $message1->toString();
        $message2 = clone $message1;
        $message2->setSubject('Message2');
        foreach ($message2->getChildren() as $child) {
            $child->setBody('Test');
            $child->getHeaders()->removeAll('X-Test-Remove');
            $child->getHeaders()->get('X-Test-Alter')->setValue('Altered');
        }
        $final = $message1->toString();
        if ($source != $final) {
            $this->fail("Difference although object cloned \n [".$source."]\n[".$final."]\n");
        }
        $final = $message2->toString();
        if ($final == $source) {
            $this->fail('Two body matches although they should differ'."\n [".$source."]\n[".$final."]\n");
        }
        $id_1 = $message1->getId();
        $id_2 = $message2->getId();
        $this->assertEquals($id_1, $id_2, 'Message Ids differ');
        $id_2 = $message2->generateId();
        $this->assertNotEquals($id_1, $id_2, 'Message Ids are the same');
    }

    // -- Private helpers
    protected function _recursiveObjectCloningCheck($obj1, $obj2, $obj1_clone)
    {
        $obj1_properties = (array) $obj1;
        $obj2_properties = (array) $obj2;
        $obj1_clone_properties = (array) $obj1_clone;

        foreach ($obj1_properties as $property => $value) {
            if (is_object($value)) {
                $obj1_value = $obj1_properties[$property];
                $obj2_value = $obj2_properties[$property];
                $obj1_clone_value = $obj1_clone_properties[$property];

                if ($obj1_value !== $obj2_value) {
                    // two separetely instanciated objects property not referencing same object
                    $this->assertFalse(
                        // but object's clone does - not everything copied
                        $obj1_value === $obj1_clone_value,
                        "Property `$property` cloning error: source and cloned objects property is referencing same object"
                    );
                } else {
                    // two separetely instanciated objects have same reference
                    $this->assertFalse(
                        // but object's clone doesn't - overdone making copies
                        $obj1_value !== $obj1_clone_value,
                        "Property `$property` not properly cloned: it should reference same object as cloning source (overdone copping)"
                    );
                }
                // recurse
                $this->_recursiveObjectCloningCheck($obj1_value, $obj2_value, $obj1_clone_value);
            } elseif (is_array($value)) {
                $obj1_value = $obj1_properties[$property];
                $obj2_value = $obj2_properties[$property];
                $obj1_clone_value = $obj1_clone_properties[$property];

                return $this->_recursiveArrayCloningCheck($obj1_value, $obj2_value, $obj1_clone_value);
            }
        }
    }

    protected function _recursiveArrayCloningCheck($array1, $array2, $array1_clone)
    {
        foreach ($array1 as $key => $value) {
            if (is_object($value)) {
                $arr1_value = $array1[$key];
                $arr2_value = $array2[$key];
                $arr1_clone_value = $array1_clone[$key];
                if ($arr1_value !== $arr2_value) {
                    // two separetely instanciated objects property not referencing same object
                    $this->assertFalse(
                        // but object's clone does - not everything copied
                        $arr1_value === $arr1_clone_value,
                        "Key `$key` cloning error: source and cloned objects property is referencing same object"
                    );
                } else {
                    // two separetely instanciated objects have same reference
                    $this->assertFalse(
                        // but object's clone doesn't - overdone making copies
                        $arr1_value !== $arr1_clone_value,
                        "Key `$key` not properly cloned: it should reference same object as cloning source (overdone copping)"
                    );
                }
                // recurse
                $this->_recursiveObjectCloningCheck($arr1_value, $arr2_value, $arr1_clone_value);
            } elseif (is_array($value)) {
                $arr1_value = $array1[$key];
                $arr2_value = $array2[$key];
                $arr1_clone_value = $array1_clone[$key];

                return $this->_recursiveArrayCloningCheck($obj1_value, $obj2_value, $obj1_clone_value);
            }
        }
    }
}
