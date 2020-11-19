<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTesingResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testing_result', function (Blueprint $table) {
            $table->id();
            $table->integer('result_score')->nullable(false)->default(0);
            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('id')->on('module')
                ->onDelete('cascade');
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
        Schema::dropIfExists('testing_result');
    }
}
