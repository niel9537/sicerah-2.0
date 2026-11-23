@extends('layouts.app')

@section('title', 'Indikator Kinerja')
@section('subtitle', $jenis === 'MAKRO' ? 'Kelola indikator makro daerah' : 'Kelola indikator ' . $jenis)

@section('content')
    <x-ui.breadcrumb :items="['Indikator' => '', $jenis => '']" />

    {{-- Header Tab + Tombol Tambah --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">

        <div class="flex flex-wrap gap-2">
            @foreach(['IKD' => 'Daerah', 'IKU' => 'Utama', 'MAKRO' => 'Makro', 'IKK' => 'Kunci'] as $code => $label)
                <a href="{{ route('indikator.index', ['jenis' => $code]) }}"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition duration-200
                    {{ $jenis === $code
                        ? 'bg-brand-gradient text-white shadow-soft'
                        : 'bg-white text-slate-600 border border-slate-200 hover:border-amber-300 hover:text-amber-800 hover:shadow-sm'
                    }}">
                    {{ $code }}
                    <span class="text-xs opacity-75">
                        ({{ $label }})
                    </span>
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('indikator.import.form', ['jenis' => $jenis]) }}"
                class="btn-secondary btn-sm inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Impor Excel
            </a>

            <button
                @click="$dispatch('open-modal-indikator-create')"
                class="btn-primary btn-sm inline-flex items-center gap-2 whitespace-nowrap">

                <svg class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>

                Tambah {{ $jenis }}
            </button>
        </div>

    </div>

    <x-ui.card>

        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 mb-5">
            <input type="hidden" name="jenis" value="{{ $jenis }}">

            <input
                type="text"
                name="q"
                value="{{ $q }}"
                placeholder="Cari indikator..."
                class="input">

            <select
                name="aspek"
                class="select"
                onchange="this.form.submit()">

                <option value="">Semua Aspek</option>

                @foreach($aspekList as $namaAspek)
                    <option value="{{ $namaAspek }}" @selected($aspek === $namaAspek)>
                        {{ Str::headline(Str::lower(str_replace('ASPEK ', '', $namaAspek))) }}
                    </option>
                @endforeach

            </select>

            <select
                name="opd_id"
                class="select"
                onchange="this.form.submit()">

                <option value="">Semua OPD</option>

                @foreach($opdList as $o)
                    <option
                        value="{{ $o->id }}"
                        @selected($opdId == $o->id)>
                        {{ $o->nama }}
                    </option>
                @endforeach

            </select>

            <button
                type="submit"
                class="btn-secondary justify-center">
                Filter
            </button>

        </form>

        <x-ui.table :headers="['Kode', 'Nama Indikator', 'Kelompok', 'Aspek', 'Satuan', 'Sumber Data', 'Aksi']">

            @forelse($items as $item)

                <tr>

                    <td class="text-xs text-slate-500 whitespace-nowrap">{{ $item->kode ?? '—' }}</td>

                    <td class="font-medium text-slate-800 max-w-sm">
                        {{ $item->nama }}
                    </td>

                    <td class="text-xs whitespace-nowrap">
                        @if($item->jenis === 'IKK')
                            <span class="badge-warning">IKK</span>
                        @elseif($item->jenis === 'IKU' && !$item->is_iku)
                            <span class="badge-maroon">IKU</span>
                        @else
                            <span class="badge-info">IKD</span>
                        @endif
                        @if($item->is_iku)<span class="badge-maroon">IKU</span>@endif
                        @if($item->is_makro)<span class="badge-success">Makro</span>@endif
                    </td>

                    <td class="text-xs max-w-xs">
                        {{ $item->aspek ? Str::headline(Str::lower(str_replace('ASPEK ', '', $item->aspek))) : '—' }}
                    </td>

                    <td>
                        <span class="badge-info">
                            {{ $item->satuan ?? '-' }}
                        </span>
                    </td>

                    <td class="text-xs">
                        {{ $item->sumber_data ?? '-' }}
                    </td>

                    <td>

                        <div class="flex items-center gap-1">

                            <a href="{{ route('indikator.kelola', ['indikator' => $item->id]) }}" class="btn-secondary btn-sm">Kelola Capaian</a>

                            <button
                                @click='$dispatch("open-modal-indikator-edit-{{ $item->id }}")'
                                class="btn-secondary btn-sm">

                                Ubah

                            </button>

                            <form
                                method="POST"
                                action="{{ route('indikator.destroy', $item) }}"
                                onsubmit="return confirm('Hapus indikator ini?')">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn-danger btn-sm">

                                    Hapus

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

                <x-ui.modal
                    id="indikator-edit-{{ $item->id }}"
                    title="Ubah Indikator"
                    size="lg">

                    <form
                        method="POST"
                        action="{{ route('indikator.update', $item) }}"
                        id="form-indikator-edit-{{ $item->id }}">

                        @csrf
                        @method('PUT')

                        @include('indikator._form', [
                            'indikator' => $item,
                            'opdList' => $opdList,
                            'urusanList' => $urusanList,
                            'sasaranList' => $sasaranList
                        ])

                    </form>

                    <x-slot:footer>

                        <button
                            type="button"
                            @click='$dispatch("close-modal-indikator-edit-{{ $item->id }}")'
                            class="btn-secondary btn-sm">

                            Batal

                        </button>

                        <button
                            type="submit"
                            form="form-indikator-edit-{{ $item->id }}"
                            class="btn-primary btn-sm">

                            Simpan Perubahan

                        </button>

                    </x-slot:footer>

                </x-ui.modal>

            @empty

                <tr>
                    <td colspan="7">
                        <x-ui.empty-state title="Belum ada indikator {{ $jenis }}" />
                    </td>
                </tr>

            @endforelse

        </x-ui.table>

        <div class="mt-4">
            {{ $items->links() }}
        </div>

    </x-ui.card>

    <x-ui.modal
        id="indikator-create"
        title="Tambah Indikator {{ $jenis }}"
        size="lg">

        <form
            method="POST"
            action="{{ route('indikator.store') }}"
            id="form-indikator-create">

            @csrf

            @include('indikator._form', [
                'indikator' => null,
                'jenisDefault' => $jenis === 'MAKRO' ? 'IKD' : $jenis,
                'isMakroDefault' => $jenis === 'MAKRO',
                'opdList' => $opdList,
                'urusanList' => $urusanList,
                'sasaranList' => $sasaranList
            ])

        </form>

        <x-slot:footer>

            <button
                type="button"
                @click="$dispatch('close-modal-indikator-create')"
                class="btn-secondary btn-sm">

                Batal

            </button>

            <button
                type="submit"
                form="form-indikator-create"
                class="btn-primary btn-sm">

                Simpan

            </button>

        </x-slot:footer>

    </x-ui.modal>

@endsection
