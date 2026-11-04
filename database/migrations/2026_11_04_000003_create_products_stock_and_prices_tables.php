<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->string('satuan')->default('Box');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('status', ['Tersedia', 'Terbatas', 'Habis'])->default('Tersedia');
            $table->timestamp('source_updated_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('harga', 15, 2);
            $table->date('berlaku_dari')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_lists');
        Schema::dropIfExists('stock_snapshots');
        Schema::dropIfExists('products');
    }
};
