<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantConnectionsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('tenant_connections', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('tenant_id')->unsigned()->index();
			$table->string('database');
			$table->timestamps();
			$table->foreign('tenant_id')->references('id')->on(str_plural(app(config('artify.tenant'))->getTable()));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('tenant_connections');
	}
}
