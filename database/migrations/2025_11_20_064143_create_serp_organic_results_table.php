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
        Schema::create('serp_organic_results', function (Blueprint $table) {
            $table->id();

            // ★ 追加カラム（OrganicKeyword 由来）
            $table->string('original_keyword'); // 元キーワード
            $table->string('product');
            $table->string('priority');

            // SERP情報
            $table->date('fetched_date');
            $table->integer('rank');
            $table->string('keyword'); // 実際の検索ワード（= base_keyword と同じでもOK）
            $table->text('url');
            $table->text('title')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serp_organic_results');
    }
};
