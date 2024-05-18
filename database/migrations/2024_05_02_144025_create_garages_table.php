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
        Schema::create('garages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('ownerId')->index();
            $table->integer('num_floors');
            $table->string('location');
            $table->double('lat');
            $table->double('longt');
            $table->double('rate');
            $table->string('garage_img');
            $table->string('desc');
            $table->decimal('price');
            $table->integer("num_spaces");
            $table->boolean('support');
            $table->boolean('security_camera');
            $table->boolean('online_payment');
            $table->boolean('emergency_exit');
            $table->timestamps();

            $table->foreign('ownerId')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garages');
    }
};
