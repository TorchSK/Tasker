<?php

use Illuminate\Database\Migrations\Migration;

class AddOrderToCovers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('banners', function($table)
		{
			$table->string('order')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('banners', function($table)
		{
			$table->dropColumn('order');	

		});

	}

}

