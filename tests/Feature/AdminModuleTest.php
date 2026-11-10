<?php

namespace Tests\Feature;

use App\Support\Admin\DataAdmin;
use Tests\TestCase;

class AdminModuleTest extends TestCase
{
    public function test_sidebar_is_grouped_with_admin_panel_badge(): void
    {
        $html = $this->get('/admin/dashboard')->assertOk()->assertSee('Admin Panel')->getContent();

        $this->assertSame(1, preg_match_all('#<nav class="relative mt-\[17px\].*?</nav>#s', $html, $nav));
        $this->assertSame(15, substr_count($nav[0][0], '<a '));
        $this->assertSeeInOrderIn($nav[0][0], [
            'RINGKASAN', 'Dashboard',
            'MASTER DATA', 'Manajemen Pengguna', 'Role &amp; Hak Akses', 'Unit / Fakultas', 'Mata Anggaran (MAK)',
            'TRANSAKSI', 'Pengajuan Surat', 'Data Pengadaan', 'Dokumen &amp; Lampiran', 'Riwayat Approval', 'Reminder', 'Penerimaan Barang', 'Pembayaran',
            'SISTEM', 'Notifikasi', 'Audit Log / Riwayat', 'Logout',
        ]);
        $this->assertStringContainsString('Super Admin · Sistem', $html);
    }

    public function test_dashboard_has_cards_table_and_links_to_tracking_detail(): void
    {
        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Ringkasan administrasi dan pengelolaan SIMPATIK-PBJ.')
            ->assertSeeInOrder(['Pengajuan Surat', 'Paket Pengadaan', 'Menunggu Tindak Lanjut', 'Total Data Terkelola'])
            ->assertSeeInOrder(['Daftar Dashboard', 'Total: 3 data', 'Modul', 'Total', 'Status', 'Aksi'])
            ->assertSee('PT Mitra Jaya')
            ->assertSee(route('admin.dashboard.show', 601))
            ->assertDontSee('+ Tambah')
            ->assertSee('Menampilkan 1-3 dari 3 data');
    }

    public function test_dashboard_detail_reuses_ppk_tracking_view_with_clickable_steps(): void
    {
        // id 603 berstatus selesai: 10 tahap semuanya dapat dibuka; id 601 (revisi) baru sampai tahap 2
        $html = $this->get('/admin/dashboard/603')
            ->assertOk()
            ->assertSee('Admin Panel')
            ->assertSee('Informasi Pengadaan')
            ->assertSee('Alur Perjalanan Pengadaan')
            ->assertSee('Pembayaran Selesai')
            ->getContent();

        $this->assertSame(10, substr_count($html, 'admin/dashboard/603/tahap/'));
        $this->assertStringNotContainsString('/ppk/tracking/', $html);
        $this->assertSame(2, substr_count($this->get('/admin/dashboard/601')->getContent(), 'admin/dashboard/601/tahap/'));
        $this->get('/admin/dashboard/999')->assertNotFound();
    }

    public function test_every_timeline_step_opens_with_admin_layout(): void
    {
        foreach (range(1, 10) as $nomor) {
            $html = $this->get("/admin/dashboard/603/tahap/{$nomor}")->assertOk()->assertSee('Admin Panel')->getContent();

            // Sidebar tetap Super Admin: tidak ada tautan menu modul lain, dan tombol kembali menuju Detail Dashboard
            $this->assertStringContainsString(route('admin.dashboard.show', 603), $html, "Tahap {$nomor}");
            $this->assertStringNotContainsString('Modul PPK', $html, "Tahap {$nomor}");
        }

        $this->get('/admin/dashboard/601/tahap/3')->assertNotFound();
        $this->get('/admin/dashboard/603/tahap/11')->assertNotFound();
    }

    public function test_every_list_page_renders_with_generic_view(): void
    {
        foreach (DataAdmin::BAGIAN as $slug) {
            $data = DataAdmin::bagian($slug);
            $respons = $this->get('/admin/'.$slug)->assertOk()->assertSee('Daftar '.e($data['judul']), false);

            foreach ($data['kolom'] as $kolom) {
                $respons->assertSee(e($kolom['label']), false);
            }
            foreach ($data['kartu'] as $kartu) {
                $respons->assertSee((string) $kartu['jumlah']);
            }
            $respons->assertSee('Cari data...')->assertSee('Menampilkan 1-', false);
        }
    }

    public function test_tambah_button_only_on_master_pages(): void
    {
        foreach (['pengguna', 'role', 'unit', 'mak', 'pengajuan'] as $slug) {
            $this->get('/admin/'.$slug)->assertSee('Tambah');
        }
        foreach (['pengadaan', 'dokumen', 'approval', 'reminder', 'penerimaan', 'pembayaran', 'notifikasi', 'audit'] as $slug) {
            $this->get('/admin/'.$slug)->assertDontSee('Tambah');
        }
    }

    public function test_manajemen_pengguna_columns_rows_and_status_pills(): void
    {
        $this->get('/admin/pengguna')
            ->assertOk()
            ->assertSeeInOrder(['Total Pengguna', 'Akun Aktif', 'Nonaktif', 'Role Terdaftar'])
            ->assertSeeInOrder(['Nama Pengguna', 'Email', 'Unit', 'Role', 'Status', 'Aksi'])
            ->assertSee('Mahfudh Al Rafif')
            ->assertSee('mahfudh@simp.pbj.id')
            ->assertSee('Data contoh')
            ->assertSee('Total: 4 data');
    }

    public function test_detail_modal_has_seven_fields_disabled_by_default_and_edit_toggle(): void
    {
        $html = $this->get('/admin/pengguna')
            ->assertOk()
            ->assertSee('Detail Pengguna')
            ->assertSeeInOrder(['ID Pengguna', 'Nama', 'Email', 'NIP', 'Unit', 'Role', 'Status'])
            ->assertSee('name="nama"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="nip"', false)
            ->assertSee('name="unit"', false)
            ->assertSee('name="role"', false)
            ->assertSee('name="status"', false)
            ->assertSee('data-label-edit="Edit Pengguna"', false)
            ->assertSee('Simpan Perubahan')
            ->assertSee('>Tutup</button>', false)
            ->assertSee('>Hapus</button>', false)
            ->assertSee('method="POST" action=""', false)
            ->assertSee('data-buka-detail-admin', false)
            ->getContent();

        // 6 isian dapat diedit (disabled sampai tombol Edit diklik) + 1 isian tetap (ID) yang selalu disabled
        $this->assertSame(7, preg_match_all('#<(input|select)[^>]*\bdisabled\b#', $html));
        $this->assertSame(6, substr_count($html, 'data-edit disabled'));
    }

    public function test_detail_modal_is_not_rendered_on_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertDontSee('data-modal-detail-admin', false);
    }

    public function test_search_filter_and_pagination(): void
    {
        $this->get('/admin/pengguna?q=dewi')->assertOk()->assertSee('Dewi Anggraini')->assertDontSee('Siti Rahma')->assertSee('Total: 1 data');
        $this->get('/admin/pengguna?status=Nonaktif')->assertOk()->assertSee('Siti Rahma')->assertDontSee('Dewi Anggraini');
        $this->get('/admin/pengguna?q=tidak-ada')->assertOk()->assertSee('Tidak ada data yang sesuai.');
        $this->get('/admin/pengguna?page=99')->assertOk()->assertSee('Menampilkan 1-4 dari 4 data');
    }

    public function test_unknown_section_returns_404(): void
    {
        $this->get('/admin/tidak-ada')->assertNotFound();
    }

    /** Memastikan potongan teks muncul berurutan di dalam HTML tertentu */
    private function assertSeeInOrderIn(string $html, array $teks): void
    {
        $posisi = 0;
        foreach ($teks as $t) {
            $ketemu = strpos($html, $t, $posisi);
            $this->assertNotFalse($ketemu, "Teks '{$t}' tidak ditemukan berurutan.");
            $posisi = $ketemu + strlen($t);
        }
    }
}
