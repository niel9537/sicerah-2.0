@extends('layouts.app')

@section('title', $title)
@section('subtitle', 'Modul sedang dalam pengembangan')

@section('content')
    <x-ui.breadcrumb :items="[$title => '']" />

    <x-ui.card>
        <x-ui.empty-state
            title="Modul {{ $title }} akan segera hadir"
            description="Modul ini sedang dibangun pada tahap berikutnya. Struktur data dan menu sudah tersedia." />
    </x-ui.card>
@endsection