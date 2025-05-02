<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('logistics_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('stops'); // Array of delivery points
            $table->integer('estimated_time_minutes');
            $table->integer('actual_time_minutes')->nullable();
            $table->integer('delay_minutes')->default(0);
            $table->json('traffic_zones_crossed'); // Array of zone IDs
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logistics_routes');
    }
}; 