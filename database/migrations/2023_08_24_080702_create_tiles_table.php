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
            $table->boolean('carving_map_needed')->default(true);
            $table->boolean('bump_map_needed')->default(true);
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
