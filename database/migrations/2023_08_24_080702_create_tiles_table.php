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
        Schema::create('tiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tilefile_id')->constrained()->onDelete('cascade');
            $table->integer('serial');
            $table->string('tilename');
            $table->string('size');
            $table->string('finish');
            $table->boolean('tile_image_needed')->default(true);
            $table->string('tile_images')->nullable();
            $table->boolean('map_image_needed')->default(true);
            $table->string('map_images')->nullable();
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
        Schema::dropIfExists('tiles');
    }
};
