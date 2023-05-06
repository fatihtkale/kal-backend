<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('task_number');
            $table->date('task_date');
            $table->time('task_time');
            $table->string('task_color')->nullable();
            $table->date('task_date_end')->nullable();
            $table->time('task_time_end')->nullable();
            $table->string('title');
            $table->json('fields')->nullable();
            $table->json('data')->nullable();
            $table->integer('is_deleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task');
    }
}
