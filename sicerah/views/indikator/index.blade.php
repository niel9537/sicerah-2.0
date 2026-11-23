@extends('layouts.app')

@section('title', 'Indikator Kinerja')
@section('subtitle', 'Kelola indikator ' . $jenis)

@section('content')
    <x-ui.breadcrumb :items="['Indikator' => '', $jenis => '']" />

    {{-- Header Tab + Tombol Tambah --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">

        <div class="flex flex-wrap gap-2">
            @foreach(['IKU' => 'Utama', 'IKK' => 'Kunci', 'IKD' => 'Daerah'] as $code => $label)
                <a href="{{ route('indikator.index', ['jenis' => $code]) }}"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition duration-200
                    {{ $jenis === $code
                        ? 'bg-brand-gradient text-white shadow-soft'
                        : 'bg-white text-slate-600 border border-slate-200 hover:border-red-300 hover:text-red-700 hover:shadow-sm'
                    }}">
                    {{ $code }}
                    <span class="text-xs opacity-75">
                        ({{ $label }})
                    </span>
                </a>
            @endforeach
        </div>

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

    <x-ui.card>

        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
            <input type="hidden" name="jenis" value="{{ $jenis }}">

            <input
                type="text"
                name="q"
                value="{{ $q }}"
                placeholder="Cari indikator..."
                class="input">

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

        <x-ui.table :headers="['Nama Indikator', 'OPD', 'Urusan', 'Satuan', 'Sumber Data', 'Aksi']">

            @forelse($items as $item)

                <tr>

                    <td class="font-medium text-slate-800 max-w-xs">
                        {{ $item->nama }}
                    </td>

                    <td class="text-xs">
                        {{ $item->opd?->nama ?? '-' }}
                    </td>

                    <td class="text-xs">
                        {{ $item->urusan?->nama ?? '-' }}
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
                    <td colspan="6">
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
                'jenisDefault' => $jenis,
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