<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->enum('type', ['retur', 'eskalasi', 'po']);
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('assigned_sales_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['baru', 'diproses', 'eskalasi_manager', 'selesai', 'ditolak'])->default('baru');
            $table->timestamp('sla_due_at')->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('nama_produk_manual')->nullable();
            $table->string('batch_number')->nullable();
            $table->integer('qty')->default(1);
            $table->string('alasan');
            $table->string('no_faktur')->nullable();
            $table->timestamps();
        });

        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->cascadeOnDelete();
            $table->string('wa_media_id')->nullable();
            $table->string('local_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->bigInteger('file_size')->nullable();
            $table->string('sha256_hash', 64)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('return_requests');
        Schema::dropIfExists('tickets');
    }
};
