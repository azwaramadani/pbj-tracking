<?php

namespace Tests\Feature;

use Tests\TestCase;

class PerencanaanModuleTest extends TestCase
{
    public function test_dashboard_shows_perencanaan_layout_cards_and_table(): void
    {
        $this->get('/perencanaan/dashboard')
            ->assertOk()
            ->assertSee('Modul Perencanaan')
            ->assertSee(route('perencanaan.notifikasi.index'))
            ->assertSee(route('perencanaan.profil.show'))
            ->assertDontSee('Modul Wadir 2')
            ->assertSeeInOrder(['Menunggu', 'MAK', 'Selesai', 'Perlu', 'Direvisi', 'Permohonan', 'Ditolak'])
            ->assertSeeInOrder(['No Surat', 'Unit Pengaju', 'Judul Pengadaan', 'Nilai Estimasi', 'Status', 'Aksi'])
            ->assertSee('Rp 120.000.000')
            ->assertSee(route('perencanaan.permohonan.show', 701));
    }

    public function test_sidebar_only_has_three_links(): void
    {
        $html = $this->get('/perencanaan/dashboard')->getContent();

        $this->assertSame(1, preg_match_all('#<nav class="relative mt-\[17px\].*?</nav>#s', $html, $nav));
        $this->assertSame(3, substr_count($nav[0][0], '<a '));
    }

    public function test_detail_in_progress_shows_editable_form_and_both_actions(): void
    {
        foreach ([701, 704] as $id) {
            $this->get("/perencanaan/permohonan/{$id}")
                ->assertOk()
                ->assertSee('Form Alokasi Anggaran (MAK)')
                ->assertSee('name="jenis_anggaran"', false)
                ->assertSee('name="mata_anggaran_kegiatan"', false)
                ->assertSee('name="jumlah_alokasi"', false)
                ->assertSee('>Keputusan</button>', false)
                ->assertSee('Pembagian MAK')
                ->assertSee(route('perencanaan.split-mak.show', $id))
                ->assertSee('id="modal-konfirmasi"', false)
                ->assertDontSee('Lembar Rekomendasi');
        }
    }

    public function test_detail_completed_shows_memos_and_read_only_form_without_actions(): void
    {
        $this->get('/perencanaan/permohonan/703')
            ->assertOk()
            ->assertSee('Lembar Rekomendasi &amp; Aksi', false)
            ->assertSee('Memo_Peretujuan_Wadir 2.pdf')
            ->assertSee('Memo_Peretujuan_PPK.pdf')
            ->assertSee('Form Alokasi Anggaran (MAK)')
            ->assertDontSee('>Keputusan</button>', false)
            ->assertDontSee('Pembagian MAK')
            ->assertDontSee('id="modal-konfirmasi"', false);
    }

    public function test_decision_modal_has_three_choices_and_empty_reason(): void
    {
        $this->get('/perencanaan/permohonan/701')
            ->assertSee('Konfirmasi Persetujuan')
            ->assertSee('value="setuju"', false)
            ->assertSee('value="revisi"', false)
            ->assertSee('value="tolak"', false)
            ->assertSee('Wajib diisi jika permohonan ditolak.');
    }

    public function test_split_mak_page_renders_rows_validation_and_diagram(): void
    {
        $this->get('/perencanaan/permohonan/701/split-mak')
            ->assertOk()
            ->assertSee('Split MAK')
            ->assertSee('Rp 100.000.000')
            ->assertSee('DITOLAK')
            ->assertSee('Tambah Baris Pembagian')
            ->assertSee('MAK Alokasi 1')
            ->assertSee('MAK Alokasi 2')
            ->assertSee('name="mak_alokasi[]"', false)
            ->assertSee('value="60.000.000"', false)
            ->assertSee('value="40.000.000"', false)
            ->assertSee('REQ-2026-0145')
            ->assertSee('Batalkan')
            ->assertSee('Simpan Pembagian Anggaran');
    }

    public function test_notification_and_profile_pages_use_perencanaan_layout(): void
    {
        foreach (['/perencanaan/notifikasi', '/perencanaan/profil'] as $url) {
            $this->get($url)->assertOk()->assertSee('Modul Perencanaan');
        }
    }

    public function test_unknown_records_return_404(): void
    {
        $this->get('/perencanaan/permohonan/999')->assertNotFound();
        $this->get('/perencanaan/permohonan/999/split-mak')->assertNotFound();
    }
}
