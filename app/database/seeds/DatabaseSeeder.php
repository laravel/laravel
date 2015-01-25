<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		User::create([
			'username' => 'test',
			'password' => Hash::make('test12345')
		]);
	}

}
