<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('slug', 50);
            $table->string('status', 20)->default('aktif'); // aktif|nonaktif
            $table->string('access_state', 20)->default('live'); // live|expired
            $table->string('tema', 50);
            $table->string('kategori', 40)->default('wedding');

            $table->string('nama_pria', 100)->nullable();
            $table->string('nama_wanita', 100)->nullable();
            $table->string('nama_anak', 100)->nullable();
            $table->string('ortu_pria', 255)->nullable();
            $table->string('ortu_wanita', 255)->nullable();

            $table->date('tanggal_akad')->nullable();
            $table->string('waktu_akad', 80)->nullable();
            $table->string('tempat_akad', 200)->nullable();
            $table->string('alamat_akad', 300)->nullable();

            $table->date('tanggal_resepsi')->nullable();
            $table->string('waktu_resepsi', 80)->nullable();
            $table->string('tempat_resepsi', 200)->nullable();
            $table->string('alamat_resepsi', 300)->nullable();

            $table->string('maps_url', 500)->nullable();
            $table->text('kutipan')->nullable();
            $table->string('kutipan_sumber', 100)->nullable();
            $table->string('youtube_url', 500)->nullable();

            $table->string('foto_wanita', 255)->nullable();
            $table->string('foto_pria', 255)->nullable();
            $table->string('foto_anak', 255)->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->string('qris_image', 255)->nullable();

            $table->unsignedInteger('views')->default(0);
            $table->json('payload_json')->nullable();

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('purge_at')->nullable();
            $table->timestamps();

            $table->unique('slug');
            $table->index(['status', 'access_state', 'slug'], 'invitations_public_lookup');
            $table->index(['access_state', 'purge_at'], 'invitations_purge_lookup');
            $table->index('updated_at');
            $table->index('expires_at');
        });

        Schema::create('invitation_wishes', function (Blueprint $table) {
            $table->id();
            $table->char('invitation_id', 36);
            $table->string('nama', 100);
            $table->text('ucapan');
            $table->string('kehadiran', 20); // hadir|tidak_hadir
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('invitation_id')->references('id')->on('invitations')->cascadeOnDelete();
            $table->index(['invitation_id', 'created_at'], 'wishes_invitation_created');
            $table->index(['invitation_id', 'kehadiran'], 'wishes_invitation_kehadiran');
        });

        Schema::create('invitation_stories', function (Blueprint $table) {
            $table->id();
            $table->char('invitation_id', 36);
            $table->string('tahun', 20)->nullable();
            $table->string('judul', 100)->nullable();
            $table->string('deskripsi', 400)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->foreign('invitation_id')->references('id')->on('invitations')->cascadeOnDelete();
            $table->index(['invitation_id', 'sort_order'], 'stories_invitation_sort');
        });

        Schema::create('invitation_accounts', function (Blueprint $table) {
            $table->id();
            $table->char('invitation_id', 36);
            $table->string('bank', 50)->nullable();
            $table->string('nomor', 50)->nullable();
            $table->string('atas_nama', 100)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->foreign('invitation_id')->references('id')->on('invitations')->cascadeOnDelete();
            $table->index('invitation_id');
        });

        Schema::create('invitation_ewallets', function (Blueprint $table) {
            $table->id();
            $table->char('invitation_id', 36);
            $table->string('tipe', 50)->nullable();
            $table->string('nomor', 50)->nullable();
            $table->string('atas_nama', 100)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->foreign('invitation_id')->references('id')->on('invitations')->cascadeOnDelete();
            $table->index('invitation_id');
        });

        Schema::create('catalog_templates', function (Blueprint $table) {
            $table->string('template_key', 50)->primary();
            $table->unsignedInteger('harga')->default(0);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->boolean('aktif_katalog')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_ewallets');
        Schema::dropIfExists('invitation_accounts');
        Schema::dropIfExists('invitation_stories');
        Schema::dropIfExists('invitation_wishes');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('catalog_templates');
    }
};
