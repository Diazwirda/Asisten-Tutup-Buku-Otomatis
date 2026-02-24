<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reconciliation_results', function (Blueprint $table) {
            $table->id();

            // metadata
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // fields yang sudah ada di UI kamu
            $table->string('sumber_data')->nullable();
            $table->string('alasan_catatan')->nullable();

            $table->string('bank_statement_id')->nullable();
            $table->string('internal_ledger_id')->nullable();

            // status rekonsiliasi: matched / unmatched / pair_not_found / pending_review
            $table->string('status_rekonsiliasi')->index();

            // untuk BI (opsional, kalau belum ada biarkan null)
            $table->string('category')->nullable();
            $table->double('amount_diff')->nullable();
            $table->integer('processing_seconds')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliation_results');
    }
};