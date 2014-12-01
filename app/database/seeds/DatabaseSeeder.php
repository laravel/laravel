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

		DB::table('categories')->truncate();

		Category::create(array(
			'name' => 'A',
			'parent_id' => null
		));

		Category::create(array(
			'name' => 'B',
			'parent_id' => null
		));

		Category::create(array(
			'name' => 'A-1',
			'parent_id' => 1
		));

		Category::create(array(
			'name' => 'A-2',
			'parent_id' => 1
		));

		Category::create(array(
			'name' => 'A-3',
			'parent_id' => 1
		));
	}

}
