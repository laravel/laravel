<?php

/**
 * @todo
 */
class Swift_Signers_OpenDKIMSignerTest extends \SwiftMailerTestCase
{
    public function setUp()
    {
        if (!extension_loaded('opendkim')) {
            $this->markTestSkipped(
                'Need OpenDKIM extension run these tests.'
             );
        }
    }

    public function testBasicSigningHeaderManipulation()
    {
    }

    // Default Signing
    public function testSigningDefaults()
    {
    }

    // SHA256 Signing
    public function testSigning256()
    {
    }

    // Relaxed/Relaxed Hash Signing
    public function testSigningRelaxedRelaxed256()
    {
    }

    // Relaxed/Simple Hash Signing
    public function testSigningRelaxedSimple256()
    {
    }

    // Simple/Relaxed Hash Signing
    public function testSigningSimpleRelaxed256()
    {
    }
}
