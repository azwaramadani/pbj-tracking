@extends('layouts.ppk')

@section('judul_halaman', 'Profil')
@section('menu_aktif', 'profil')

@section('konten')
<x-profil-panel :user="$user" />
@endsection
