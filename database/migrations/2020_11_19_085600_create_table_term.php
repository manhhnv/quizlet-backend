<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTerm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term', function (Blueprint $table) {
            $table->id();
            $table->string('question', 255)->nullable(false);
            $table->string('explain', 255)->nullable(true);
            $table->integer('score')->nullable(true);
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
        Schema::dropIfExists('term');
    }
}
