<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ads_keyword_planner_results', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->string('avg_monthly_search_volume')->nullable();
            $table->string('competition_level')->nullable();
            $table->string('competition_index')->nullable();
            $table->decimal('low_cpc', 10, 2)->nullable();
            $table->decimal('high_cpc', 10, 2)->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_keyword_planner');
    }
};
