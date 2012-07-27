<?php

class {{class}} {

  /**
   * Make changes to the database.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('{{table}}', function($table)
    {
      $table->increments('id');
    });
  }

  /**
   * Revert the changes to the database.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('{{table}}');
  }

}