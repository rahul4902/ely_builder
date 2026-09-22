<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('schedulers')) {
            Schema::create('schedulers', function (Blueprint $table) {
                $table->increments('id');
                $table->date('from_date');
                $table->date('to_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->json('user_ids');
                // $table->integer('user_id')->unsigned()->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedulers');
    }
}
