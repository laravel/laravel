<?php


use BehatEditor\Models\User;
use Illuminate\Support\Facades\Cache;

class InspireTest extends TestCase {


    /**
     * @test
     */
    public function console_inspired_command_not_loaded()
    {
        exec("php artisan inspire", $results);
        $this->assertGreaterThan(0, count($results));
    }

}
