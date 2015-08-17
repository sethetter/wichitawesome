<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->dateTime('start_time');
			$table->dateTime('end_time');
			$table->integer('venue_id')->unsigned();
			$table->string('image')->nullable();
			$table->bigInteger('facebook')->unsigned()->nullable()->index();
			$table->string('hashtag')->nullable();
			$table->text('description')->nullable();
			$table->integer('user_id')->unsigned()->nullable();
			$table->boolean('visible')->default(false);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('events');
	}

}
