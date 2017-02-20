<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LocalisationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
    	// __('argument') fails when 'argument' is exactly the name of a lang file (stored in resources/lang/en/argument.php).
    	// It looks lile the function reads the lang file array and tries to display it.
    	// Expected behaviour: _('argument') should return the string 'argument' or a corresponding label from a json language file.

        $this->assertEquals(__('pagination'), 'pagination');
    }
}
