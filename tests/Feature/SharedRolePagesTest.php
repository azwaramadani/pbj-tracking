<?php

namespace Tests\Feature;

use Tests\TestCase;

class SharedRolePagesTest extends TestCase
{
    public function test_ppk_shared_pages_keep_the_ppk_layout_and_links(): void
    {
        foreach ([
            ['/ppk/dashboard', 'ppk.dashboard', 'ppk.permohonan.show', 101],
            ['/ppk/pembayaran', 'ppk.pembayaran.index', 'ppk.pembayaran.show', 201],
        ] as [$url, $filterRoute, $detailRoute, $id]) {
            $this->get($url)
                ->assertOk()
                ->assertSee('Modul PPK')
                ->assertSee(route('ppk.tracking.index'))
                ->assertSee('action="'.route($filterRoute).'"', false)
                ->assertSee(route($detailRoute, $id))
                ->assertDontSee('Modul Wadir 2')
                ->assertDontSee(route('wadir2.dashboard'));
        }

        $this->get('/ppk/permohonan/101')
            ->assertOk()
            ->assertSee('Modul PPK')
            ->assertSee(route('ppk.dashboard'))
            ->assertDontSee('>Kirim Ke PPBJ</button>', false)
            ->assertSee('>Tentukan Keputusan</button>', false);

        $this->get('/ppk/pembayaran/201')
            ->assertOk()
            ->assertSee('Modul PPK')
            ->assertSee(route('ppk.pembayaran.index'))
            ->assertDontSee('data-buka-modal="modal-konfirmasi"', false);
    }

    public function test_wadir2_shared_pages_preserve_their_layout_links_and_payment_action(): void
    {
        foreach ([
            ['/wadir2/dashboard', 'wadir2.dashboard', 'wadir2.permohonan.show', 101],
            ['/wadir2/pembayaran', 'wadir2.pembayaran.index', 'wadir2.pembayaran.show', 201],
        ] as [$url, $filterRoute, $detailRoute, $id]) {
            $this->get($url)
                ->assertOk()
                ->assertSee('Modul Wadir 2')
                ->assertSee('action="'.route($filterRoute).'"', false)
                ->assertSee(route($detailRoute, $id))
                ->assertDontSee('Modul PPK');
        }

        $this->get('/wadir2/permohonan/101')
            ->assertOk()
            ->assertSee('Modul Wadir 2')
            ->assertSee(route('wadir2.dashboard'))
            ->assertSee('>Kirim Ke PPBJ</button>', false);

        $this->get('/wadir2/pembayaran/201')
            ->assertOk()
            ->assertSee('Modul Wadir 2')
            ->assertSee(route('wadir2.pembayaran.index'))
            ->assertSee('data-buka-modal="modal-konfirmasi"', false);
    }

    public function test_tracking_payment_detail_keeps_tracking_navigation(): void
    {
        $this->get('/ppk/tracking/603/tahap/9')
            ->assertOk()
            ->assertSee('Modul PPK')
            ->assertSee(route('ppk.tracking.show', 603))
            ->assertSee('PPK')
            ->assertDontSee('data-buka-modal="modal-konfirmasi"', false);
    }
}
