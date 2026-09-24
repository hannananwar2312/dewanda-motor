<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Harga modal di produk (master)
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('cost_price')->default(0)->after('price');
        });

        // 2) Snapshot harga modal & jual di detail penjualan
        //    (harga jual sudah ada di kolom 'price', kita tambah modal)
        Schema::table('sale_items', function (Blueprint $table) {
            $table->bigInteger('cost_price')->default(0)->after('price');
        });

        // 3) Tabel modal (modal awal + tambahan modal)
        Schema::create('capitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('tambahan');   // awal / tambahan
            $table->bigInteger('amount')->default(0);
            $table->date('date');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        // 4) Tabel pengeluaran operasional (di luar pembelian produk)
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category');                     // listrik, internet, dll
            $table->bigInteger('amount')->default(0);
            $table->date('date');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('capitals');

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
    }
};