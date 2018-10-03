<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTourpointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_points', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('name of tour point');
            $table->double('latitude')->comment('geolocation latitude of tour point');
            $table->double('longitude')->comment('geolocation longitude of tour point');
            $table->enum('category', ['park', 'museum', 'restaurant'])->comment('category predefined');
            $table->enum('visibility', ['public', 'private'])->comment('visibility of tour point, public everyone see, private only creator');
            $table->integer('user_id')->unsigned()->comment('id of user creator of tour point');
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
        Schema::dropIfExists('tour_points');
    }
}
