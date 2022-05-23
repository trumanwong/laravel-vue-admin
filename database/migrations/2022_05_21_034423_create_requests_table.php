<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('method');
            $table->string('uri');
            $table->string('ip');
            $table->string('visit_at');
        });

        Schema::table('requests', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('alter table requests modify ip VARBINARY(16)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
