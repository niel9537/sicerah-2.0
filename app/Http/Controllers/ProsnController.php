<?php

namespace App\Http\Controllers;

use App\Models\Prosn;
use App\Models\ProsnValidasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProsnController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'tema' => ['nullable', Rule::in(['A', 'B', 'C', 'D', 'E'])],
            'status' => ['nullable', Rule::in(Prosn::STATUS)],
            'dokumen' => ['nullable', Rule::in(Prosn::DOKUMEN)],
            'q' => ['nullable', 'string', 'max:200'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $all = Prosn::with('validasiTerakhir')->where('tahun', 2026)->orderBy('baris')->get();
        $themes = $all->where('jenis', 'tema');
        $indicators = $all->where('jenis', 'indikator');
        $filtered = $indicators->filter(function ($row) use ($filters) {
            foreach (['tema', 'status', 'dokumen' => 'ketersediaan'] as $key => $field) {
                $key = is_int($key) ? $field : $key;
                if (! empty($filters[$key]) && $filters[$key] !== $row->$field) {
                    return false;
                }
            }
            $q = trim($filters['q'] ?? '');

            return $q === '' || mb_stripos(implode(' ', [$row->kode, $row->uraian, $row->indikator, $row->bukti_1, $row->catatan]), $q) !== false;
        });
        $perPage = 5;
        $page = min((int) ($filters['page'] ?? 1), max(1, (int) ceil($filtered->count() / $perPage)));
        $pagination = new LengthAwarePaginator(
            $filtered->values()->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->except('page'), 'fragment' => 'matriks']
        );
        $pageItems = $pagination->getCollection();
        // Repeat theme and program headings on every page that contains their indicators.
        $rows = $all->filter(fn ($row) => $row->jenis === 'indikator' ? $pageItems->contains('id', $row->id) : $pageItems->contains(fn ($i) => $i->tema === $row->tema && ($row->jenis === 'tema' || $i->program === $row->program)));

        return view('prosn.index', compact('all', 'themes', 'indicators', 'filtered', 'rows', 'filters', 'pagination'));
    }

    public function edit(Prosn $prosn)
    {
        abort_unless($prosn->tahun === 2026, 404);

        return view('prosn.edit', compact('prosn'));
    }

    public function update(Request $request, Prosn $prosn)
    {
        abort_unless($prosn->tahun === 2026, 404);
        $rules = ['uraian' => ['required', 'string', 'max:15000'], 'catatan' => ['nullable', 'string', 'max:10000'], 'tautan' => ['nullable', 'array:1']];
        foreach ([1] as $i) {
            $rules['bukti_'.$i] = ['nullable', 'string', 'max:2000'];
            $rules['tautan.'.$i] = ['nullable', 'url:http,https', 'max:2000'];
        }
        if ($prosn->jenis === 'indikator') {
            $rules += ['status' => ['required', Rule::in(Prosn::STATUS)], 'ketersediaan' => ['required', Rule::in(Prosn::DOKUMEN)], 'indikator' => ['required', 'string', 'max:15000']];
        }
        $data = $request->validate($rules);
        DB::transaction(function () use ($prosn, $data) {
            $locked = Prosn::lockForUpdate()->findOrFail($prosn->id);
            $locked->fill($data);
            if ($locked->isDirty()) {
                $locked->versi++;
                $locked->save();
            }
        });

        return redirect()->route('prosn.index', ['tema' => $prosn->tema])->with('success', 'Data eMonev ProSN berhasil disimpan.');
    }

    public function validasi(Prosn $prosn)
    {
        abort_unless($prosn->tahun === 2026 && $prosn->jenis === 'indikator', 404);
        $prosn->load('riwayatValidasi', 'validasiTerakhir');

        return view('prosn.validasi', compact('prosn'));
    }

    public function validateItem(Request $request, Prosn $prosn)
    {
        abort_unless($prosn->tahun === 2026 && $prosn->jenis === 'indikator', 404);
        $data = $request->validate([
            'keputusan' => ['required', Rule::in(['setuju', 'revisi'])],
            'catatan' => ['nullable', 'required_if:keputusan,revisi', 'string', 'max:10000'],
            'versi' => ['required', 'integer', 'min:1'],
        ]);
        DB::transaction(function () use ($request, $prosn, $data) {
            $locked = Prosn::lockForUpdate()->findOrFail($prosn->id);
            if ($locked->versi !== (int) $data['versi']) {
                throw ValidationException::withMessages(['versi' => 'Data telah diubah admin. Muat ulang halaman dan periksa bukti terbaru sebelum memvalidasi.']);
            }
            ProsnValidasi::create([
                'prosn_id' => $locked->id, 'versi' => $locked->versi,
                'user_id' => $request->user()->id, 'nama_validator' => $request->user()->nama,
                'keputusan' => $data['keputusan'], 'catatan' => $data['catatan'] ?? null,
                'validated_at' => now(),
            ]);
        });

        return redirect()->route('prosn.validasi', $prosn)->with('success', 'Keputusan validasi berhasil disimpan.');
    }

    public function export()
    {
        $sheet = new Spreadsheet;
        $tab = $sheet->getActiveSheet()->setTitle('Pelaporan');
        $headers = ['NO', 'PROGRAM / KEGIATAN', 'STATUS', 'INDIKATOR KINERJA', 'KODE', 'BUKTI DUKUNG', 'KETERSEDIAAN DOKUMEN', 'CATATAN', 'VALIDASI INSPEKTORAT', 'CATATAN VALIDASI', 'OLEH', 'TANGGAL DAN JAM (WIB)'];
        $tab->fromArray($headers, null, 'A1');
        $rows = Prosn::with('validasiTerakhir')->where('tahun', 2026)->orderBy('baris')->get();
        foreach ($rows as $i => $row) {
            $r = $i + 2;
            $v = $row->validasiTerakhir;
            $current = $v && (int) $v->versi === $row->versi;
            $values = [$row->nomor, $row->uraian, $row->status, $row->indikator, $row->kode, $row->bukti_1, $row->ketersediaan, $row->catatan];
            $values = array_merge($values, $row->jenis === 'indikator' ? [$row->status_validasi, $current ? $v->catatan : '', $current ? $v->nama_validator : '', $current ? $v->validated_at->timezone('Asia/Jakarta')->format('d-m-Y H:i:s').' WIB' : ''] : ['', '', '', '']);
            foreach ($values as $c => $value) {
                $tab->setCellValueExplicit([$c + 1, $r], $value ?? '', DataType::TYPE_STRING);
            }
            $url = $row->tautan[1] ?? null;
            if ($url && preg_match('~^https?://~i', $url)) {
                $tab->getCell('F'.$r)->getHyperlink()->setUrl($url);
                $tab->getStyle('F'.$r)->getFont()->setUnderline(true)->getColor()->setARGB('FF0051A8');
            }
            if ($row->jenis !== 'indikator') {
                $tab->getStyle('A'.$r.':L'.$r)->getFill()->setFillType('solid')->getStartColor()->setARGB($row->jenis === 'tema' ? 'FFCBD5E1' : 'FFF1F5F9');
                $tab->getStyle('A'.$r.':L'.$r)->getFont()->setBold(true);
            }
        }
        $tab->freezePane('C2');
        $tab->setAutoFilter('A1:L'.$tab->getHighestRow());
        foreach (['A' => 8, 'B' => 65, 'C' => 20, 'D' => 70, 'E' => 13, 'F' => 26, 'G' => 23, 'H' => 32, 'I' => 23, 'J' => 35, 'K' => 25, 'L' => 27] as $c => $w) {
            $tab->getColumnDimension($c)->setWidth($w);
        }
        $this->formatReport($tab, 'L');
        $history = $sheet->createSheet()->setTitle('Riwayat Validasi');
        $history->fromArray(['KODE', 'VERSI DATA', 'KEPUTUSAN', 'CATATAN', 'OLEH', 'TANGGAL DAN JAM (WIB)'], null, 'A1');
        $ids = $rows->keyBy('id');
        foreach (ProsnValidasi::whereIn('prosn_id', $ids->keys())->orderBy('id')->get() as $i => $v) {
            foreach ([$ids[$v->prosn_id]->kode, $v->versi, ucfirst($v->keputusan), $v->catatan, $v->nama_validator, $v->validated_at->timezone('Asia/Jakarta')->format('d-m-Y H:i:s').' WIB'] as $c => $value) {
                $history->setCellValueExplicit([$c + 1, $i + 2], (string) ($value ?? ''), DataType::TYPE_STRING);
            }
        }
        foreach (['A' => 15, 'B' => 15, 'C' => 18, 'D' => 70, 'E' => 30, 'F' => 28] as $c => $w) {
            $history->getColumnDimension($c)->setWidth($w);
        }
        $this->formatReport($history, 'F');
        $sheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($sheet) {
            (new Xlsx($sheet))->save('php://output');
            $sheet->disconnectWorksheets();
        }, 'eMonev_ProSN_2026.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    private function formatReport(Worksheet $tab, string $last): void
    {
        $tab->getStyle($tab->calculateWorksheetDimension())->getAlignment()->setWrapText(true)->setVertical('top');
        $tab->getStyle('A1:'.$last.'1')->getFont()->setBold(true);
        $tab->getPageSetup()->setOrientation('landscape')->setPaperSize(PageSetup::PAPERSIZE_A3)->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(1, 1);
        $tab->getPageSetup()->setPrintArea('A1:'.$last.$tab->getHighestRow());
        $tab->getHeaderFooter()->setOddHeader('&C&BMatriks eMonev ProSN Tahun 2026');
        $tab->getHeaderFooter()->setOddFooter('&LKabupaten Nias Barat&RHalaman &P / &N');
    }
}
