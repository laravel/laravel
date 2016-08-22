<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FirstOrNewTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        if (Schema::hasTable('first_or_new_demo')) {
            Schema::drop('first_or_new_demo');
        }

        Schema::create('first_or_new_demo', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $this->faker = Faker\Factory::create();

    }

    /**
     * Works correctly when searched value is mass assignable
     *
     * @test
     * @return void
     */
    public function it_passes_when_id_is_mass_assignable()
    {
        // Create three new test rows [1, 2, 3]
        for ($j=0; $j < 3; $j++) {
            \WorkingDemo::create(['name' => $this->faker->name]);
        }

        // This should be an existing row
        $workingDemo = \WorkingDemo::firstOrNew(['id' => 3]);

        $this->assertEquals(3, $workingDemo->id);
    }

    /**
     * FirstOrNew should return a valid model but not when searching by a
     * non-massasignable field
     *
     * @test
     * @return void
     */
    public function it_fails_when_id_is_not_mass_assignable()
    {
        // Create three new test rows [1, 2, 3]
    	for ($i=0; $i < 3; $i++) {
            \Demo::create(['name' => $this->faker->name]);
        }

        // This should be an existing row
        $failDemo = \Demo::firstOrNew(['id' => 3]);

        $this->assertEquals(3, $failDemo->id);
    }

    public function tearDown()
    {
        Schema::drop('first_or_new_demo');
    }
}

/**
 * Model to show failure if ID is not mass-assignable
 */
class Demo extends Eloquent
{
    protected $table = 'first_or_new_demo';
    protected $fillable = ['name'];
}

/**
 * Model with mass-assignabale ID
 */
class WorkingDemo extends Eloquent
{
    protected $table = 'first_or_new_demo';
    protected $fillable = ['id', 'name'];
}