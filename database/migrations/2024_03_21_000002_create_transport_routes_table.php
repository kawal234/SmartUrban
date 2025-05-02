<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // bus, train, etc.
            $table->json('route_points'); // Array of coordinates
            $table->integer('usage_rate')->default(0);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('frequency_minutes');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transport_routes');
    }
}; 