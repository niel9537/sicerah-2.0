<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiRenja;
use App\Models\Periode;
use App\Models\SubKegiatan;
use App\Models\TargetTahunan;
use App\Services\EvaluasiRenjaService;
use App\Traits\HasFlashMessage;
use App\Traits\LogsAudit;
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
 * Impor/ekspor massal Evaluasi Renja (realisasi fisik & keuangan per triwulan) lewat Excel.
 * Meniru persis aturan halaman Kelola Evaluasi manual:
 *  - Target Tahunan untuk sub kegiatan + tahun itu harus sudah ada (sama seperti simpanTriwulan()).
 *  - Baris yang sudah berstatus "diajukan"/"diverifikasi" TIDAK ditimpa — dilewati & dilaporkan,
 *    supaya impor tidak bisa memotong alur verifikasi yang sedang berjalan.
 *  - Setiap create/update tetap memanggil EvaluasiRenjaService::syncRealisasiTriwulanan(),
 *    persis seperti yang dipanggil EvaluasiRenjaController, supaya angka di Dasbor Kinerja,
 *    halaman Realisasi Kinerja, dan landing page ikut ter-update — bukan cuma tabel evaluasi_renja.
 *
 * Perlu: composer require phpoffice/phpspreadsheet
 */
class EvaluasiImportController extends Controller
{
    use HasFlashMessage, LogsAudit;

    private const KOLOM = [
        'Sub Kegiatan Kode', 'Sub Kegiatan Nama', 'Tahun', 'Triwulan',
        'Realisasi Fisik', 'Realisasi Keuangan',
        'Keterangan', 'Analisis', 'Kendala', 'Solusi', 'Tindak Lanjut',
    ];

    public function __construct(private readonly EvaluasiRenjaService $evaluasiService)
    {
    }

    public function form(Request $request): View
    {
        $tahun = (int) $request->input('tahun', now()->year);

        return view('evaluasi.import', compact('tahun'));
    }

    /**
     * Template — terisi seluruh sub kegiatan untuk tahun terpilih, beserta evaluasi yang
     * sudah ada per triwulan (kalau ada), supaya admin tinggal mengedit angkanya.
     */
    public function template(Request $request): StreamedResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $subKegiatans = SubKegiatan::query()
            ->with([
                'kegiatan.program.opd',
                'evaluasiRenjas' => fn ($q) => $q->whereHas('periode', fn ($p) => $p->where('tahun', $tahun))->with('periode'),
            ])
            ->orderBy('kode')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Evaluasi Renja');

        $sheet->fromArray(self::KOLOM, null, 'A1');
        $sheet->getStyle('A1:K1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8B1E30');

        $baris = 2;
        foreach ($subKegiatans as $sub) {
            $evaluasiPerTw = $sub->evaluasiRenjas->keyBy(fn ($e) => $e->periode->triwulan ?? null);

            foreach ([1, 2, 3, 4] as $tw) {
                $ev = $evaluasiPerTw->get($tw);

                $sheet->fromArray([
                    $sub->kode,
                    $sub->nama,
                    $tahun,
                    $tw,
                    $ev->realisasi_fisik ?? '',
                    $ev->realisasi_keuangan ?? '',
                    $ev->keterangan ?? '',
                    $ev->analisis ?? '',
                    $ev->kendala ?? '',
                    $ev->solusi ?? '',
                    $ev->tindak_lanjut ?? '',
                ], null, "A{$baris}");

                $baris++;
            }
        }

        foreach (range('A', 'K') as $kolom) {
            $sheet->getColumnDimension($kolom)->setAutoSize(true);
        }
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->freezePane('A2');

        $namaFile = 'template-evaluasi-renja-'.$tahun.'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

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

        foreach (['sub kegiatan nama', 'tahun', 'triwulan'] as $wajib) {
            if (! array_key_exists($wajib, $kolomIndex) && ! array_key_exists('sub kegiatan kode', $kolomIndex)) {
                return $this->flashError("Kolom '{$wajib}' tidak ditemukan. Gunakan header sesuai template.");
            }
        }

        $berhasil = 0;
        $dilewati = [];
        $nomorBaris = 1;
        $romawiKeAngka = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4];

        foreach ($rows as $row) {
            $nomorBaris++;

            $ambil = function (string $nama) use ($kolomIndex, $row) {
                if (! isset($kolomIndex[$nama])) {
                    return null;
                }
                $nilai = $row[$kolomIndex[$nama]] ?? null;

                return $nilai === null ? null : trim((string) $nilai);
            };

            $subKode  = $ambil('sub kegiatan kode');
            $subNama  = $ambil('sub kegiatan nama');
            $tahun    = $ambil('tahun');
            $twMentah = strtoupper((string) $ambil('triwulan'));

            if (blank($subKode) && blank($subNama) && blank($tahun)) {
                continue; // baris kosong, lewati diam-diam
            }

            $triwulan = $romawiKeAngka[$twMentah] ?? (is_numeric($twMentah) ? (int) $twMentah : null);

            if (! is_numeric($tahun) || ! in_array($triwulan, [1, 2, 3, 4], true)) {
                $dilewati[] = "Baris {$nomorBaris}: Tahun/Triwulan tidak valid (Tahun=\"{$tahun}\", Triwulan=\"{$twMentah}\").";
                continue;
            }

            $sub = null;
            if (filled($subKode)) {
                $sub = SubKegiatan::whereRaw('LOWER(TRIM(kode)) = ?', [strtolower($subKode)])->first();
            }
            if (! $sub && filled($subNama)) {
                $sub = SubKegiatan::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower($subNama)])->first();
            }

            if (! $sub) {
                $dilewati[] = "Baris {$nomorBaris}: Sub Kegiatan \"{$subNama}\" tidak ditemukan.";
                continue;
            }

            $periode = Periode::where('tahun', (int) $tahun)->where('triwulan', $triwulan)->first();
            if (! $periode) {
                $dilewati[] = "Baris {$nomorBaris}: Periode Triwulan {$triwulan} tahun {$tahun} belum dibuat (menu Master Data → Periode).";
                continue;
            }

            $target = TargetTahunan::where('subkegiatan_id', $sub->id)->where('tahun', (int) $tahun)->first();
            if (! $target) {
                $dilewati[] = "Baris {$nomorBaris}: Target Tahunan {$tahun} untuk \"{$sub->nama}\" belum diatur — isi dulu lewat menu Target Tahunan.";
                continue;
            }

            $realisasiFisik    = $ambil('realisasi fisik');
            $realisasiKeuangan = $ambil('realisasi keuangan');

            if (filled($realisasiFisik) && ! is_numeric($realisasiFisik)) {
                $dilewati[] = "Baris {$nomorBaris}: Realisasi Fisik (\"{$realisasiFisik}\") bukan angka.";
                continue;
            }
            if (filled($realisasiKeuangan) && ! is_numeric($realisasiKeuangan)) {
                $dilewati[] = "Baris {$nomorBaris}: Realisasi Keuangan (\"{$realisasiKeuangan}\") bukan angka.";
                continue;
            }

            $evaluasi = EvaluasiRenja::where('subkegiatan_id', $sub->id)->where('periode_id', $periode->id)->first();

            if ($evaluasi && ! in_array($evaluasi->status, [EvaluasiRenja::STATUS_DRAFT, EvaluasiRenja::STATUS_DITOLAK], true)) {
                $dilewati[] = "Baris {$nomorBaris}: \"{$sub->nama}\" TW{$triwulan} {$tahun} sudah berstatus {$evaluasi->statusLabel()}, tidak ditimpa oleh impor.";
                continue;
            }

            $payload = array_filter([
                'realisasi_fisik'    => $realisasiFisik !== null ? $realisasiFisik : null,
                'realisasi_keuangan' => $realisasiKeuangan !== null ? $realisasiKeuangan : null,
                'keterangan'         => $ambil('keterangan'),
                'analisis'           => $ambil('analisis'),
                'kendala'            => $ambil('kendala'),
                'solusi'             => $ambil('solusi'),
                'tindak_lanjut'      => $ambil('tindak lanjut'),
            ], fn ($v) => $v !== null && $v !== '');

            if (empty($payload)) {
                continue; // tidak ada apa pun untuk disimpan di baris ini
            }

            try {
                DB::transaction(function () use ($evaluasi, $payload, $sub, $periode, $target, &$berhasil) {
                    if ($evaluasi) {
                        $dataLama = $evaluasi->getOriginal();
                        $evaluasi->update($payload);

                        if ($evaluasi->status === EvaluasiRenja::STATUS_DITOLAK) {
                            $evaluasi->update(['status' => EvaluasiRenja::STATUS_DRAFT, 'catatan_verifikasi' => null]);
                        }

                        $this->logAudit('update', $evaluasi, $dataLama);
                    } else {
                        $evaluasi = EvaluasiRenja::create([
                            ...$payload,
                            'subkegiatan_id'  => $sub->id,
                            'periode_id'      => $periode->id,
                            'target_fisik'    => $target->target_kinerja,
                            'target_keuangan' => $target->paguEfektif(),
                            'status'          => EvaluasiRenja::STATUS_DRAFT,
                            'created_by'      => auth()->id(),
                        ]);

                        $this->logAudit('create', $evaluasi);
                    }

                    $this->evaluasiService->syncRealisasiTriwulanan($evaluasi);
                });

                $berhasil++;
            } catch (\Throwable $e) {
                $dilewati[] = "Baris {$nomorBaris}: gagal disimpan ({$e->getMessage()}).";
            }
        }

        session(['import_hasil' => ['berhasil' => $berhasil, 'dilewati' => $dilewati]]);

        return redirect()->route('evaluasi.import.form')->with(
            'success',
            "{$berhasil} baris berhasil diimpor sebagai draft.".(count($dilewati) ? ' '.count($dilewati).' baris perlu dicek.' : '')
        );
    }
}
