<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alt')->nullable();
            $table->string('path');
            $table->string('mime');
            $table->string('extension');
            $table->tinyInteger('status')->default(1);
            $table->boolean('main')->default(false);
            $table->unsignedBigInteger('gallery_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('gallery_id')->references('id')->on('galleries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign(['gallery_id']);
        });
        Schema::dropIfExists('images');
    }
};
