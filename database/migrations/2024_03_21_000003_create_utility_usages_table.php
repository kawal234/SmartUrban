<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('utility_usages', function (Blueprint $table) {
            $table->id();
            $table->string('utility_type');
            $table->decimal('usage_amount', 10, 2);
            $table->string('unit');
            $table->date('date');
            $table->string('status')->default('normal');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('utility_usages');
    }
}; 