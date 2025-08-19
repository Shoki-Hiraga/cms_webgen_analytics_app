<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ga4_filter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ga4_setting_id')->unique(); // リレーション
            $table->string('session_medium_filter')->default('organic');
            $table->timestamps();

            $table->foreign('ga4_setting_id')
                ->references('id')
                ->on('ga4_setting')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ga4_filter');
    }
};
