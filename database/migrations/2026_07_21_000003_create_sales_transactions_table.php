<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id();
            $table->char('invitation_id', 36)->nullable();
            $table->string('slug', 50)->nullable();
            $table->string('template_key', 50);
            $table->string('template_nama', 100)->nullable();
            $table->string('kategori', 40)->nullable();
            $table->string('pelanggan', 200)->nullable();
            $table->unsignedInteger('harga_asli')->default(0);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->unsignedInteger('harga_final')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index('template_key');
            $table->index('invitation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_transactions');
    }
};
