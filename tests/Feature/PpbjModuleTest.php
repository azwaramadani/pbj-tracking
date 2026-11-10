<?php

namespace Tests\Feature;

use Tests\TestCase;

class PpbjModuleTest extends TestCase
{
    public function test_sidebar_has_five_links_and_ppbj_badge(): void
    {
        $html = $this->get('/ppbj/dashboard')->assertOk()->assertSee('Modul PPBJ')->getContent();

        $this->assertSame(1, preg_match_all('#<nav class="relative mt-\[17px\].*?</nav>#s', $html, $nav));
        $this->assertSame(5, substr_count($nav[0][0], '<a '));
        foreach (['Dashboard', 'Kontrak &amp; SPK', 'Pembayaran', 'Notifikasi', 'Profil'] as $label) {
            $this->assertStringContainsString($label, $nav[0][0]);
        }
    }

    public function test_dashboard_has_four_cards_and_columns(): void
    {
        $this->get('/ppbj/dashboard')
            ->assertOk()
            ->assertSeeInOrder(['Menunggu', 'Tinjauan', 'Kontrak', 'Aktif', 'Perlu', 'Direvisi', 'Upload', 'Selesai'])
            ->assertSeeInOrder(['No Surat', 'Jenis Pengadaan', 'Judul Pengadaan', 'Status', 'PIC Pengadaan', 'Aksi'])
            ->assertSee('JASA')->assertSee('BARANG')
            ->assertSee('REVISI')->assertSee('TINJAUAN')
            ->assertSee(route('ppbj.pengadaan.show', 901));
    }

    public function test_kontrak_and_pembayaran_dashboards_reuse_layout_with_other_data(): void
    {
        $this->get('/ppbj/kontrak')
            ->assertOk()
            ->assertSeeInOrder(['Perlu', 'Unggah', 'Kontrak', 'Aktif', 'Kontrak', 'Selesai'])
            ->assertSeeInOrder(['No Surat', 'Nomor SPK', 'Judul Pengadaan', 'Status', 'Tanggal', 'Aksi'])
            ->assertSee(route('ppbj.kontrak.show', 951));

        $this->get('/ppbj/pembayaran')
            ->assertOk()
            ->assertSeeInOrder(['Perlu', 'Unggah', 'Menunggu', 'Persetujuan', 'Terbitkan', 'Pembayaran', 'Menunggu', 'Pembayaran', 'Pengadaan', 'Selesai'])
            ->assertSee('PERSETUJUAN')->assertSee('PEMBAYARAN')
            ->assertSee(route('ppbj.pembayaran.show', 1001));
    }

    public function test_metode_pengadaan_default_state_is_editable_with_upload(): void
    {
        $this->get('/ppbj/pengadaan/901')
            ->assertOk()
            ->assertSee('Pencatatan Metode Pengadaan')
            ->assertSee('name="metode_pengadaan"', false)
            ->assertSee('name="jenis_pengadaan"', false)
            ->assertSee('name="vendor"', false)
            ->assertSee('name="dokumen_resmi"', false)
            ->assertSee('Tarik berkas atau pilih file')
            ->assertSee('Lihat Permohonan')
            ->assertSee('>Kirim</button>', false)
            ->assertDontSee('Permintaan Perbaikan')
            ->assertDontSee('DI LUAR SIMPATIK-PBJ');
    }

    public function test_metode_pengadaan_revisi_shows_revision_request_on_top(): void
    {
        $html = $this->get('/ppbj/pengadaan/902')
            ->assertOk()
            ->assertSee('Permintaan Perbaikan')
            ->assertSee('Lengkapi klausul garansi minimum 3 tahun')
            ->assertSee('Persetujuan dan tanda tangan tidak dapat diterapkan selama revisi belum diselesaikan.')
            ->assertSee('name="dokumen_resmi"', false)
            ->getContent();

        $this->assertLessThan(strpos($html, 'Pencatatan Metode Pengadaan'), strpos($html, 'Permintaan Perbaikan'));
    }

    public function test_metode_pengadaan_tinjauan_is_read_only_with_blue_alert(): void
    {
        $this->get('/ppbj/pengadaan/904')
            ->assertOk()
            ->assertSee('Pelaksanaan Inaproc / SPSE dilakukan DI LUAR SIMPATIK-PBJ.')
            ->assertSee('Pencatatan Metode Pengadaan')
            ->assertDontSee('type="file"', false)
            ->assertDontSee('Tarik berkas atau pilih file')
            ->assertDontSee('>Kirim</button>', false)
            ->assertDontSee('<form method="POST"', false)
            ->assertDontSee('name="metode_pengadaan"', false);
    }

    public function test_lihat_permohonan_reuses_detail_permohonan_view_in_ppbj_layout(): void
    {
        $this->get('/ppbj/pengadaan/901/permohonan')
            ->assertOk()
            ->assertSee('Modul PPBJ')
            ->assertSee('Keputusan Persetujuan')
            ->assertSee('Memo Persetujuan Wadir 2')
            ->assertSee('Form Alokasi Anggaran (MAK)')
            ->assertSee(route('ppbj.pengadaan.show', 901))
            ->assertDontSee('Modul PPK');
    }

    public function test_detail_kontrak_button_follows_status(): void
    {
        $this->get('/ppbj/kontrak/951')
            ->assertOk()
            ->assertSee('Modul PPBJ')
            ->assertSee('Informasi Kontrak')
            ->assertSee('Pengingat Pengiriman')
            ->assertSee('Dokumen Kontrak &amp; Pendukung', false)
            ->assertSee('>Unggah Dokumen</a>', false)
            ->assertSee(route('ppbj.kontrak.edit', 951))
            ->assertDontSee('Lihat Penerimaan');

        foreach ([953, 954] as $id) {
            $this->get("/ppbj/kontrak/{$id}")
                ->assertOk()
                ->assertSee('>Lihat Penerimaan</a>', false)
                ->assertSee(route('ppbj.kontrak.penerimaan', $id))
                ->assertDontSee('Unggah Dokumen');
        }
    }

    public function test_unggah_kontrak_form_has_data_dokumen_fields(): void
    {
        $this->get('/ppbj/kontrak/951/unggah')
            ->assertOk()
            ->assertSee('Unggah Kontrak &amp; SPK', false)
            ->assertSee('Data Dokumen')
            ->assertSee('name="dokumen_resmi"', false)
            ->assertSee('name="nomor_dokumen"', false)
            ->assertSee('name="nomor_pengadaan"', false)
            ->assertSee('name="nilai"', false)
            ->assertSee('name="status"', false)
            ->assertSee('name="tanggal_terbit"', false)
            ->assertSee('name="tanggal_mulai"', false)
            ->assertSee('name="target_pengiriman"', false)
            ->assertSee('name="tanggal_pengingat"', false)
            ->assertSee('Lihat Pengadaan')
            ->assertSee('Kirim Kontrak');
    }

    public function test_lihat_penerimaan_is_fully_read_only(): void
    {
        foreach ([953, 954] as $id) {
            $this->get("/ppbj/kontrak/{$id}/penerimaan")
                ->assertOk()
                ->assertSee('Modul PPBJ')
                ->assertSee('Dokumentasi Penerimaan')
                ->assertDontSee('Kirim Bukti Penerimaan')
                ->assertDontSee('Unggah Semua')
                ->assertDontSee('Hapus Foto')
                ->assertDontSee('type="file"', false)
                ->assertDontSee('id="modal-penerimaan"', false)
                ->assertDontSee('name="nama_penerima"', false);
        }

        $this->get('/ppbj/kontrak/953/penerimaan')->assertSee('Barang belum diterima')->assertDontSee('berhasil dicatat');
        $this->get('/ppbj/kontrak/954/penerimaan')->assertSee('berhasil dicatat');
    }

    public function test_pembayaran_tinjauan_can_upload_and_send_with_modal(): void
    {
        $this->get('/ppbj/pembayaran/1001')
            ->assertOk()
            ->assertSee('Unggah Dokumen Pembayaran')
            ->assertSee('Dasar Pembayaran')
            ->assertSee('Lihat Dokumentasi Penerimaan')
            ->assertSee('Unggah dokumen pendukung proses')
            ->assertSee('name="dokumen_pembayaran"', false)
            ->assertSee('Spesifikasi Teknis')
            ->assertSee('data-buka-modal-ppbj="modal-kirim-persetujuan"', false)
            ->assertSee('id="modal-kirim-persetujuan"', false)
            ->assertSee('Kirim Untuk Persetujuan')
            ->assertSee('KTR/PBJ/2026/0145')
            ->assertSee('Rp450.000.000');
    }

    public function test_pembayaran_persetujuan_is_read_only_with_disabled_send(): void
    {
        $this->get('/ppbj/pembayaran/1002')
            ->assertOk()
            ->assertSee('Invoice')
            ->assertSee('Spesifikasi Teknis')
            ->assertSee('Kirim untuk Persetujuan')
            ->assertDontSee('Unggah dokumen pendukung proses')
            ->assertDontSee('name="dokumen_pembayaran"', false)
            ->assertDontSee('data-buka-modal-ppbj', false);
    }

    public function test_persetujuan_paket_enables_issue_button_only_when_ready(): void
    {
        $this->get('/ppbj/pembayaran/1003')
            ->assertOk()
            ->assertSee('Persetujuan Paket Pembayaran')
            ->assertSee('Perintah Pembayaran')
            ->assertSee('Terbitkan Perintah Pembayaran?')
            ->assertSee('PAY/PBJ/2026/0145')
            ->assertSee('Mahfudh Al Rafif — PPBJ')
            ->assertSee('Keuangan — Linda Lestari')
            ->assertSee('data-buka-modal-ppbj="modal-terbitkan-perintah"', false)
            ->assertSee('id="modal-terbitkan-perintah"', false);

        $this->get('/ppbj/pembayaran/1004')
            ->assertOk()
            ->assertSee('Terbitkan Perintah Pembayaran?')
            ->assertDontSee('data-buka-modal-ppbj', false)
            ->assertDontSee('id="modal-terbitkan-perintah"', false);
    }

    public function test_pembayaran_selesai_reuses_keuangan_view_in_ppbj_layout(): void
    {
        $this->get('/ppbj/pembayaran/1005')
            ->assertOk()
            ->assertSee('Modul PPBJ')
            ->assertSee('Bukti Realisasi Pembayaran')
            ->assertDontSee('Modul Keuangan');
    }

    public function test_notification_profile_and_unknown_records(): void
    {
        foreach (['/ppbj/notifikasi', '/ppbj/profil'] as $url) {
            $this->get($url)->assertOk()->assertSee('Modul PPBJ');
        }

        foreach (['/ppbj/pengadaan/1', '/ppbj/kontrak/1', '/ppbj/pembayaran/1', '/ppbj/kontrak/1/penerimaan'] as $url) {
            $this->get($url)->assertNotFound();
        }
    }
}
