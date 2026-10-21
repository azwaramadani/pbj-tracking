<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul_halaman', 'Dashboard') - SIMPATIK-PBJ</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface font-sans text-gray-900 antialiased">

<!-- Kerangka HTML dasar untuk semua role. Setiap layout role mengisi bagian "badan" -->
@yield('badan')

@stack('skrip')
</body>
</html>
