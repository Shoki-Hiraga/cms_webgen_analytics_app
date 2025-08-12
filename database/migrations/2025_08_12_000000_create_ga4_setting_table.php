<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ga4_setting', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // idを主キー
            $table->string('session_medium_filter')->default('organic');
            $table->longText('service_account_json'); // JSON文字列を直接保存
            $table->string('property_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ga4_setting');
    }
};
