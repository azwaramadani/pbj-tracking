@extends('layouts.admin')

@section('judul_halaman', 'Tambah Pengguna')

@section('deskripsi_halaman', 'Tambahkan akun pengguna baru ke dalam sistem SIMPATIK-PBJ.')

@section('menu_aktif', 'pengguna')

@section('konten')

<div class="mx-auto max-w-4xl">

    {{-- Tombol kembali --}}
    <div class="mb-6">
        <a href="{{ route('admin.pengguna.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-teal-600">
            <span>&larr;</span>
            Kembali ke Manajemen Pengguna
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        {{-- Header form --}}
        <div class="border-b border-gray-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-gray-800">
                Form Tambah Pengguna
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Lengkapi informasi berikut untuk membuat akun pengguna baru.
            </p>
        </div>

        <form action="{{ route('admin.pengguna.store') }}" method="POST" class="p-6">
            @csrf

            {{-- Pesan error validasi --}}
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="mb-2 font-semibold">Terdapat kesalahan:</p>
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                {{-- Nama lengkap --}}
                <div class="md:col-span-2">
                    <label for="Nama_lengkap" class="mb-2 block text-sm font-medium text-gray-700">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="Nama_lengkap"
                        name="Nama_lengkap"
                        value="{{ old('Nama_lengkap') }}"
                        required
                        maxlength="255"
                        placeholder="Masukkan nama lengkap"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                    >
                </div>

                {{-- Email --}}
                <div>
                    <label for="Email_user" class="mb-2 block text-sm font-medium text-gray-700">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="Email_user"
                        name="Email_user"
                        value="{{ old('Email_user') }}"
                        required
                        maxlength="255"
                        placeholder="contoh@email.com"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                    >
                </div>

                {{-- NIP --}}
                <div>
                    <label for="NIP" class="mb-2 block text-sm font-medium text-gray-700">
                        NIP <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="NIP"
                        name="NIP"
                        value="{{ old('NIP') }}"
                        required
                        maxlength="30"
                        placeholder="Masukkan NIP"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                    >
                </div>

                {{-- Unit --}}
                <div>
                    <label for="Asal_unit" class="mb-2 block text-sm font-medium text-gray-700">
                        Asal Unit
                    </label>
                    <input
                        type="text"
                        id="Asal_unit"
                        name="Asal_unit"
                        value="{{ old('Asal_unit') }}"
                        maxlength="255"
                        placeholder="Masukkan asal unit"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                    >
                </div>

                {{-- Role --}}
                <div>
                    <label for="Id_role" class="mb-2 block text-sm font-medium text-gray-700">
                        Role Pengguna <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="Id_role"
                        name="Id_role"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                    >
                        <option value="">Pilih Role</option>
                        @foreach ($roles as $role)
                            <option
                                value="{{ $role->Id_role }}"
                                @selected(old('Id_role') == $role->Id_role)
                            >
                                {{ $role->Nama_role }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label for="Status_user" class="mb-2 block text-sm font-medium text-gray-700">
                        Status Akun <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="Status_user"
                        name="Status_user"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                    >
                        <option value="Aktif" @selected(old('Status_user', 'Aktif') === 'Aktif')>
                            Aktif
                        </option>
                        <option value="Nonaktif" @selected(old('Status_user') === 'Nonaktif')>
                            Nonaktif
                        </option>
                    </select>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                    >
                </div>

                {{-- Konfirmasi password --}}
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Ulangi password"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                    >
                </div>

            </div>

            {{-- Informasi --}}
            <div class="mt-6 rounded-lg bg-teal-50 p-4 text-sm text-teal-800">
                <p class="font-semibold">Informasi</p>
                <p class="mt-1">
                    Password harus minimal 8 karakter dan tidak boleh sama dengan password akun pengguna lainnya.
                </p>
            </div>

{{-- Tombol Aksi - Pengujian Tampilan --}}
<div style="display: flex; width: 100%; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb;">

    <a
        href="{{ route('admin.pengguna.index') }}"
        style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; color: #374151; text-decoration: none;"
    >
        Batal
    </a>

    <button
        type="submit"
        style="flex: 1; display: flex !important; align-items: center; justify-content: center; padding: 12px; border: none; border-radius: 8px; background-color: #0d9488 !important; color: white !important; font-weight: 600; cursor: pointer; visibility: visible !important; opacity: 1 !important;"
    >
        Simpan Pengguna
    </button>

</div>


</form>
</div>
</div>

@endsection
