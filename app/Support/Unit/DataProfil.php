<?php

namespace App\Support\Unit;

/**
 * Sumber data contoh (dummy) untuk halaman Profil (dipakai bersama oleh semua role).
 *
 * Saat tabel pengguna tersedia, kembalikan auth()->user() (beserta relasi unit dan peran) dari pengguna().
 * Komponen x-profil-panel dan x-profil-modal hanya membaca properti berikut, jadi model Eloquent dengan
 * accessor bernama sama (atau objek apa pun) dapat langsung dipakai:
 *
 *   name, status, role, unit, institution, nip, email, phone, phone_masked, access_level,
 *   password_changed_at, last_login, active_session, role_description, assignment_status,
 *   notify_status_reminder (bool), notify_weekly_summary (bool)
 */
class DataProfil
{
    /** Data profil pengguna yang sedang login */
    public static function pengguna(): object
    {
        return (object) [
            'name'                   => 'Hendra Wijaya, M.T.',
            'status'                 => 'Aktif',
            'role'                   => 'Pejabat Pembuat Komitmen (PPK)',
            'unit'                   => 'Unit Perencanaan & IT',
            'institution'            => 'Fakultas Teknik',
            'nip'                    => '19880214 201001 1 003',
            'email'                  => 'hendra.wijaya@unit.ac.id',
            'phone'                  => '0812 3456 4521',
            'phone_masked'           => '08•• •••• 4521',
            'access_level'           => 'Pengelola Pengadaan',

            // Keamanan akun
            'password_changed_at'    => '12 Sep 2026',
            'last_login'             => '28 Okt 2026 · 08:14 WIB',
            'active_session'         => 'Browser saat ini · Perangkat dikenal',

            // Peran & hak akses
            'role_description'       => 'Dapat meninjau paket, mengelola penugasan, dan memantau proses.',
            'assignment_status'      => 'Aktif sejak 02 November 2026',

            // Preferensi notifikasi (true = aktif)
            'notify_status_reminder' => true,
            'notify_weekly_summary'  => true,
        ];
    }
}
