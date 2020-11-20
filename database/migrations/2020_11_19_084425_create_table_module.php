<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable(false);
            $table->integer('max_score')->nullable(false);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('class_id')->nullable(true);
            $table->unsignedBigInteger('folder_id')->nullable(true);
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('class')
                ->onDelete('cascade');
            $table->foreign('folder_id')->references('id')->on('folder')
                ->onDelete('cascade');
            $table->boolean('public')->nullable(false)->default(false);
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
        Schema::dropIfExists('module');
    }
}
