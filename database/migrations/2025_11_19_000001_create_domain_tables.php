<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('nip', 30)->unique();
            $table->string('nik', 30)->unique();
            $table->string('email', 100)->unique();
            $table->string('telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('password', 255);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->enum('tingkat', ['SD', 'SMP', 'SMA']);
            $table->string('tahun_ajaran', 9);
            $table->foreignId('wali_kelas_id')->nullable()->constrained('gurus');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['nama', 'tingkat', 'tahun_ajaran'], 'uniq_kelas');
        });

        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 20)->unique();
            $table->enum('tingkat', ['SD', 'SMP', 'SMA'])->default('SMP');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])->default('Senin');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('guru_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->enum('role', ['wali_kelas', 'guru_mapel']);
            $table->foreignId('kelas_id')->nullable()->constrained('kelas');
            $table->foreignId('mapel_id')->nullable()->constrained('mata_pelajarans');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['role', 'kelas_id'], 'uniq_wali_kelas');
            $table->unique(['guru_id', 'role', 'mapel_id'], 'uniq_guru_mapel');
            $table->index('guru_id', 'idx_guru_roles_guru');
            $table->index('mapel_id', 'idx_guru_roles_mapel');
        });

        Schema::create('guru_mapel_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus');
            $table->foreignId('mapel_id')->constrained('mata_pelajarans');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->string('tahun_ajaran', 9);
            $table->enum('semester', ['ganjil', 'genap']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['guru_id', 'mapel_id', 'kelas_id', 'tahun_ajaran', 'semester'], 'uniq_gmk');
            $table->index('tahun_ajaran', 'idx_gmk_tahun');
            $table->index('semester', 'idx_gmk_semester');
        });

        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('nisn', 20)->unique();
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nama_ayah', 100);
            $table->string('nama_ibu', 100);
            $table->text('alamat');
            $table->string('agama', 20);
            $table->string('email', 100)->unique()->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('asal_sekolah', 100);
            $table->string('password', 255);
            $table->foreignId('kelas_id')->nullable()->constrained('kelas');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('absensi_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('mapel_id')->nullable()->constrained('mata_pelajarans');
            $table->foreignId('guru_id')->constrained('gurus');
            $table->enum('tipe_absensi', ['kelas', 'mapel']);
            $table->date('tanggal');
            $table->enum('status', ['masuk', 'izin', 'sakit', 'terlambat', 'alpa']);
            $table->text('keterangan')->nullable();
            $table->string('tahun_ajaran', 9);
            $table->enum('semester', ['ganjil', 'genap']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::dropIfExists('sessions');
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('token', 512)->unique();
            $table->enum('role', ['guru', 'admin', 'wali_kelas', 'siswa']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
            $table->index('user_id', 'idx_user_id');
        });

        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('wali_kelas_id')->nullable()->constrained('gurus')->cascadeOnDelete();
            $table->enum('role', ['admin', 'guru', 'wali_kelas']);
            $table->date('tanggal');
            $table->text('deskripsi');
            $table->boolean('is_done')->default(false);
            $table->time('jam_dibuat')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index(['role', 'admin_id'], 'idx_todo_role_admin');
            $table->index(['role', 'guru_id'], 'idx_todo_role_guru');
            $table->index(['role', 'wali_kelas_id'], 'idx_todo_role_wali');
            $table->index('tanggal', 'idx_todo_tanggal');
        });

        Schema::create('kelas_siswas', function (Blueprint $table) {
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->primary(['siswa_id', 'kelas_id']);
        });

        Schema::create('mapel_siswas', function (Blueprint $table) {
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->primary(['siswa_id', 'mata_pelajaran_id']);
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('token', 512)->unique();
            $table->string('platform', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index('user_id', 'idx_device_tokens_user_id');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->string('type', 100);
            $table->text('payload')->nullable();
            $table->unsignedBigInteger('recipient');
            $table->boolean('read')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index('recipient', 'idx_notifications_recipient');
            $table->index('type', 'idx_notifications_type');
        });

        Schema::dropIfExists('password_reset_tokens');
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100);
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['admin', 'guru', 'wali_kelas', 'siswa']);
            $table->string('token_hash', 255);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index('email', 'idx_password_reset_email');
            $table->index(['user_id', 'role'], 'idx_password_reset_user');
            $table->index(['email', 'used_at'], 'idx_password_reset_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('mapel_siswas');
        Schema::dropIfExists('kelas_siswas');
        Schema::dropIfExists('todos');
        Schema::dropIfExists('absensi_siswas');
        Schema::dropIfExists('guru_mapel_kelas');
        Schema::dropIfExists('guru_roles');
        Schema::dropIfExists('mata_pelajarans');
        Schema::dropIfExists('siswas');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('gurus');
        Schema::dropIfExists('admins');

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
};
