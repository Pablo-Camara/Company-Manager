<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnomaliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->unsignedBigInteger('physical_space_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('physical_space_id')->references('id')->on('physical_spaces')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anomalies');
    }
}
