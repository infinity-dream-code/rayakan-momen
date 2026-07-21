<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_categories', function (Blueprint $table) {
            $table->string('slug', 50)->primary();
            $table->string('nama', 100);
            $table->string('tagline', 200)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('icon', 50)->default('fa-layer-group');
            $table->string('warna', 20)->default('#c9a84c');
            $table->boolean('aktif')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('image_url', 500)->nullable();
            $table->string('cloudinary_id', 255)->nullable();
            $table->timestamps();
        });

        $now = now()->toDateTimeString();
        $sort = 0;
        foreach (config('templates.categories', []) as $slug => $cat) {
            DB::table('catalog_categories')->insert([
                'slug' => $slug,
                'nama' => $cat['nama'] ?? ucfirst($slug),
                'tagline' => $cat['tagline'] ?? null,
                'deskripsi' => $cat['deskripsi'] ?? null,
                'icon' => $cat['icon'] ?? 'fa-layer-group',
                'warna' => $cat['warna'] ?? '#c9a84c',
                'aktif' => true,
                'sort_order' => $sort++,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_categories');
    }
};
