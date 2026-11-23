<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\IndikatorRealisasi;
use App\Models\IndikatorTarget;
use App\Services\BankDataIndicatorImporter;
use App\Traits\HasFlashMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Impor/ekspor massal target & realisasi capaian indikator (IKU/IKK/IKD) lewat Excel,
 * supaya admin tidak perlu mengetik satu-satu di halaman Kelola Capaian untuk banyak
 * indikator sekaligus. Perlu: composer require phpoffice/phpspreadsheet
 */
class IndikatorImportController extends Controller
{
    use HasFlashMessage;

    /** Urutan kolom yang dipakai baik saat menulis template maupun membaca file upload. */
    private const KOLOM = ['ID', 'Jenis', 'Indikator', 'Satuan', 'Tahun', 'Target', 'Realisasi TW1', 'Realisasi TW2', 'Realisasi TW3', 'Realisasi TW4'];

    public function form(Request $request): View
    {
        $jenis = strtoupper((string) $request->input('jenis', 'IKD'));
        if (! in_array($jenis, ['IKU', 'IKK', 'IKD'], true)) {
            $jenis = 'IKD';
        }

        return view('indikator.import', compact('jenis'));
    }

    /**
     * Template Excel — diisi otomatis dengan indikator yang sudah ada (ID, Jenis, Nama, Satuan)
     * plus target/realisasi tahun terpilih kalau sudah ada, jadi admin tinggal mengedit angkanya
     * (pola ekspor → edit → impor ulang), bukan mengetik nama indikator dari nol.
     */
    public function template(Request $request): StreamedResponse
    {
        $jenis = strtoupper((string) $request->input('jenis', 'IKU'));
        $tahun = (int) $request->input('tahun', now()->year);

        $indikators = $this->indicatorQueryFor($jenis)->orderBy('nama')->get();

        $targets = IndikatorTarget::whereIn('indikator_id', $indikators->pluck('id'))
            ->where('tahun', $tahun)->get()->keyBy('indikator_id');

        $realisasis = IndikatorRealisasi::whereIn('indikator_id', $indikators->pluck('id'))
            ->where('tahun', $tahun)->get()->groupBy('indikator_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Capaian Indikator');

        $sheet->fromArray(self::KOLOM, null, 'A1');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8B1E30');

        $baris = 2;
        foreach ($indikators as $ind) {
            $t = $targets->get($ind->id);
            $r = $realisasis->get($ind->id, collect());

            $sheet->fromArray([
                $ind->id,
                $ind->jenis,
                $ind->nama,
                $ind->satuan,
                $tahun,
                $t?->getRawOriginal('target') ?? '',
                $r->firstWhere('triwulan', 1)?->getRawOriginal('realisasi') ?? '',
                $r->firstWhere('triwulan', 2)?->getRawOriginal('realisasi') ?? '',
                $r->firstWhere('triwulan', 3)?->getRawOriginal('realisasi') ?? '',
                $r->firstWhere('triwulan', 4)?->getRawOriginal('realisasi') ?? '',
            ], null, "A{$baris}");

            $baris++;
        }

        if ($indikators->isEmpty()) {
            $sheet->setCellValue('A2', '(belum ada indikator ' . $jenis . ' — tambah dulu lewat menu Indikator sebelum impor)');
        }

        foreach (range('A', 'J') as $kolom) {
            $sheet->getColumnDimension($kolom)->setAutoSize(true);
        }
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->freezePane('A2');

        $namaFile = 'template-capaian-'.strtolower($jenis).'-'.$tahun.'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Proses file yang diupload. Setiap baris diproses independen (dibungkus transaksi per
     * baris, bukan satu transaksi besar) — supaya satu baris bermasalah cukup dilewati dan
     * dilaporkan, tidak menggagalkan baris-baris lain yang valid.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        } catch (\Throwable $e) {
            return $this->flashError('File tidak bisa dibaca. Pastikan formatnya .xlsx, .xls, atau .csv.');
        }

        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        if (empty($rows)) {
            return $this->flashError('File kosong.');
        }

        $header = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($rows));
        $kolomIndex = [];
        foreach ($header as $i => $nama) {
            if ($nama !== '') {
                $kolomIndex[$nama] = $i;
            }
        }

        foreach (['jenis', 'indikator', 'tahun'] as $wajib) {
            if (! array_key_exists($wajib, $kolomIndex)) {
                return $this->flashError("Kolom '{$wajib}' tidak ditemukan. Gunakan header sesuai template (unduh dulu kalau belum punya).");
            }
        }

        $berhasil = 0;
        $dilewati = [];
        $nomorBaris = 1; // baris 1 = header di file aslinya

        foreach ($rows as $row) {
            $nomorBaris++;

            $ambil = function (string $nama) use ($kolomIndex, $row) {
                if (! isset($kolomIndex[$nama])) {
                    return null;
                }
                $nilai = $row[$kolomIndex[$nama]] ?? null;

                return $nilai === null ? null : trim((string) $nilai);
            };

            $id     = $ambil('id');
            $jenis  = strtoupper((string) $ambil('jenis'));
            $nama   = $ambil('indikator');
            $satuan = $ambil('satuan');
            $tahun  = $ambil('tahun');

            // Baris benar-benar kosong (mis. sisa baris kosong di bawah data) → lewati diam-diam.
            if (blank($id) && blank($nama) && blank($tahun)) {
                continue;
            }

            if (! is_numeric($tahun)) {
                $dilewati[] = "Baris {$nomorBaris}: kolom Tahun (\"{$tahun}\") harus angka.";
                continue;
            }

            $indikator = null;
            if (filled($id) && is_numeric($id)) {
                $indikator = $this->indicatorQueryFor($jenis)->whereKey((int) $id)->first();
            }
            if (! $indikator && filled($nama) && in_array($jenis, ['IKU', 'IKK', 'IKD'], true)) {
                $indikator = $this->indicatorQueryFor($jenis)
                    ->whereRaw('LOWER(TRIM(nama)) = ?', [strtolower(trim($nama))])
                    ->first();
            }

            if (! $indikator) {
                $dilewati[] = "Baris {$nomorBaris}: indikator \"{$nama}\" jenis {$jenis} tidak ditemukan di sistem (impor tidak membuat indikator baru).";
                continue;
            }

            try {
                $catatanBaris = [];

                DB::transaction(function () use ($indikator, $satuan, $tahun, $ambil, &$catatanBaris) {
                    if (filled($satuan) && $satuan !== $indikator->satuan) {
                        $indikator->update(['satuan' => $satuan]);
                    }

                    $target = $ambil('target');
                    if (filled($target)) {
                        IndikatorTarget::updateOrCreate(
                            ['indikator_id' => $indikator->id, 'tahun' => (int) $tahun],
                            ['target' => mb_substr($target, 0, 100)]
                        );
                    }

                    foreach ([1, 2, 3, 4] as $tw) {
                        $nilai = $ambil("realisasi tw{$tw}");
                        if (! filled($nilai)) {
                            continue;
                        }
                        IndikatorRealisasi::updateOrCreate(
                            ['indikator_id' => $indikator->id, 'tahun' => (int) $tahun, 'triwulan' => $tw],
                            ['realisasi' => mb_substr($nilai, 0, 100), 'status' => 'draft']
                        );
                    }
                });

                $berhasil++;
                if ($catatanBaris) {
                    $dilewati[] = "Baris {$nomorBaris}: tersimpan sebagian — ".implode('; ', $catatanBaris).'.';
                }
            } catch (\Throwable $e) {
                $dilewati[] = "Baris {$nomorBaris}: gagal disimpan ({$e->getMessage()}).";
            }
        }

        session(['import_hasil' => ['berhasil' => $berhasil, 'dilewati' => $dilewati]]);

        return redirect()->route('indikator.import.form')->with(
            'success',
            "{$berhasil} baris berhasil diimpor.".(count($dilewati) ? ' '.count($dilewati).' baris perlu dicek (lihat rincian di bawah).' : '')
        );
    }

    public function importBankData(Request $request, BankDataIndicatorImporter $importer): RedirectResponse
    {
        $request->validate([
            'bank_data' => 'required|file|mimes:xlsx,xls|max:20480',
        ]);

        try {
            $result = $importer->import($request->file('bank_data')->getRealPath());
        } catch (\Throwable $exception) {
            return $this->flashError('Impor Bank Data gagal: '.$exception->getMessage());
        }

        return redirect()->route('indikator.index', ['jenis' => 'IKD'])->with(
            'success',
            "Bank Data tersinkron: {$result['indikator']} IKD, {$result['iku']} IKU, {$result['makro']} indikator makro, tanpa mengubah IKK."
        );
    }

    private function indicatorQueryFor(string $jenis)
    {
        return match ($jenis) {
            'IKU' => Indikator::query()->iku(),
            'IKD' => Indikator::query()->ikd(),
            default => Indikator::query()->where('jenis', $jenis),
        };
    }
}
