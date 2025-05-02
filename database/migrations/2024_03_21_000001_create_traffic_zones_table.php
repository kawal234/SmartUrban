<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('traffic_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('congestion_level')->default(0); // 0-100
            $table->string('status')->default('normal');
            $table->json('boundaries')->nullable(); // Polygon coordinates
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('traffic_zones');
    }
}; 