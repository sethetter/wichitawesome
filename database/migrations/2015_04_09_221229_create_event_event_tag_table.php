<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventEventTagTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_event_tag', function(Blueprint $table)
		{
			$table->integer('event_id')->unsigned()->references('id')->on('events');
			$table->integer('event_tag_id')->unsigned()->references('id')->on('event_tags');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('event_event_tag');
	}

}
