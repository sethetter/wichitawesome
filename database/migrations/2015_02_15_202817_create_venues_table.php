<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('venues', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('slug')->unique()->index();
			$table->string('street');
			$table->string('city');
			$table->char('state', 2);
			$table->string('zip');
			$table->decimal('longitude', 10, 6);
			$table->decimal('latitude', 10, 6);
			$table->string('image')->nullable();
			$table->bigInteger('facebook')->unsigned()->nullable()->index();
			$table->string('twitter')->nullable();
			$table->string('website')->nullable();
			$table->string('email')->nullable();
			$table->string('phone')->nullable();
			$table->text('description')->nullable();
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
		Schema::drop('venues');
	}

}
