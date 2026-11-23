{{--
    <x-ui.updated-badge>
    Indikator "kapan data ini terakhir diperbarui" yang dipakai di seluruh SICERAH:
    dasbor, kelola capaian indikator, realisasi kinerja, dan landing page publik.

    Props:
    - at        : Carbon|string|null  waktu update (boleh null kalau belum pernah)
    - label     : string   teks di depan waktu relatif, default "Diperbarui"
    - emptyText : string   teks kalau $at null
    - variant   : 'chip' | 'text'   'chip' = pil dengan latar, 'text' = teks polos kecil
    - icon      : bool     tampilkan ikon jam atau tidak
--}}
@props([
    'at' => null,
    'label' => 'Diperbarui',
    'emptyText' => 'Belum ada data',
    'variant' => 'chip',
    'icon' => true,
])

@php
    $carbon = null;

    if ($at instanceof \Illuminate\Support\Carbon) {
        $carbon = $at;
    } elseif ($at instanceof \DateTimeInterface) {
        $carbon = \Illuminate\Support\Carbon::instance($at);
    } elseif (is_string($at) && trim($at) !== '') {
        try {
            $carbon = \Illuminate\Support\Carbon::parse($at);
        } catch (\Throwable $e) {
            $carbon = null;
        }
    }

    $relatif = null;
    $exact = null;
    $stale = false; // lebih dari 90 hari dianggap perlu perhatian

    if ($carbon) {
        $exact = $carbon->format('d/m/Y H:i');
        $detik = (int) abs($carbon->diffInSeconds(now()));

        if ($detik < 60) {
            $relatif = 'baru saja';
        } elseif ($detik < 3600) {
            $relatif = intdiv($detik, 60) . ' menit yang lalu';
        } elseif ($detik < 86400) {
            $relatif = intdiv($detik, 3600) . ' jam yang lalu';
        } elseif ($detik < 86400 * 7) {
            $relatif = intdiv($detik, 86400) . ' hari yang lalu';
        } elseif ($detik < 86400 * 30) {
            $relatif = intdiv($detik, 86400 * 7) . ' minggu yang lalu';
        } elseif ($detik < 86400 * 365) {
            $relatif = intdiv($detik, 86400 * 30) . ' bulan yang lalu';
        } else {
            $relatif = intdiv($detik, 86400 * 365) . ' tahun yang lalu';
        }

        $stale = $detik > (86400 * 90);
    }

    $dotClass = ! $carbon
        ? 'bg-slate-300'
        : ($stale ? 'bg-amber-500' : 'bg-emerald-500');

    $teks = $carbon
        ? trim(($label !== '' ? "{$label} " : '') . $relatif)
        : $emptyText;
@endphp

@if($variant === 'chip')
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-medium text-slate-600']) }}
          @if($exact) title="{{ trim($label . ' ' . $exact)}}" @endif>
        <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }} flex-shrink-0"></span>
        @if($icon)
            <svg class="w-3 h-3 flex-shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @endif
        <span>{{ $teks }}</span>
    </span>
@else
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 text-xs text-slate-400']) }}
          @if($exact) title="{{ trim($label . ' ' . $exact) }}" @endif>
        @if($icon)
            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @endif
        {{ $teks }}
    </span>
@endif
