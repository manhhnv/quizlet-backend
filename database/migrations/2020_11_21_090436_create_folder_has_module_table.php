<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderHasModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_has_module', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id');
            $table->unsignedBigInteger('module_id');
            $table->foreign('folder_id')->references('id')->on('folder')
                ->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('module')
                ->onDelete('cascade');
            $table->primary(array('folder_id', 'module_id'));
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
        Schema::dropIfExists('folder_has_module');
    }
}
