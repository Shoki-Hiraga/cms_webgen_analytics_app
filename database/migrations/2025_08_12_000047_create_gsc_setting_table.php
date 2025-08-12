<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gsc_setting', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // id固定
            $table->string('site_url');
            $table->longText('service_account_json'); // JSON文字列を直接保存
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gsc_setting');
    }
};
