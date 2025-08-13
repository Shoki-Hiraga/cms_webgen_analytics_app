<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dataseting', function (Blueprint $table) {
            $table->id();
            $table->string('target'); // 'GA4' or 'GSC'
            $table->integer('start_year');
            $table->integer('start_month');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dataseting');
    }
};
