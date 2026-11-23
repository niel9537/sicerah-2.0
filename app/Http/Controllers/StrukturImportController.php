<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Opd;
use App\Models\Program;
use App\Models\SasaranPd;
use App\Models\SubKegiatan;
use App\Models\Urusan;
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
 * Impor/ekspor massal struktur nomenklatur Urusan → Program → Kegiatan → Sub Kegiatan
 * lewat Excel — satu baris mewakili satu Sub Kegiatan beserta induknya. Berbeda dari
 * IndikatorImportController: yang ini BOLEH membuat Program/Kegiatan/Sub Kegiatan baru
 * kalau belum ada (bukan cuma mencocokkan yang sudah ada), karena inilah struktur dasar
 * yang memang biasanya diisi massal dari dokumen RKPD/DPA. OPD tetap harus sudah ada
 * (tidak auto-dibuat) karena datanya lebih dari sekadar nama.
 *
 * Perlu: composer require phpoffice/phpspreadsheet (sama seperti IndikatorImportController)
 */
class StrukturImportController extends Controller
{
    use HasFlashMessage;

    private const KOLOM = [
        'Urusan Kode', 'Urusan Nama', 'OPD',
        'Program Kode', 'Program Nama', 'Sasaran PD',
        'Kegiatan Kode', 'Kegiatan Nama',
        'Sub Kegiatan Kode', 'Sub Kegiatan Nama', 'Indikator Kinerja', 'Satuan',
    ];

    public function form(): View
    {
        $opdList = Opd::orderBy('nama')->get();

        return view('master.struktur.import', compact('opdList'));
    }

    /**
     * Template — terisi seluruh struktur yang sudah ada (opsional difilter per OPD),
     * supaya admin bisa mengedit langsung lalu impor ulang, atau menambah baris baru
     * di bawahnya untuk struktur yang belum ada.
     */
    public function template(Request $request): StreamedResponse
    {
        $opdId = $request->input('opd_id');

        $subKegiatans = SubKegiatan::query()
            ->with(['kegiatan.program.urusan', 'kegiatan.program.opd', 'kegiatan.program.sasaranPd'])
            ->when($opdId, fn ($q) => $q->whereHas('kegiatan.program', fn ($p) => $p->where('opd_id', $opdId)))
            ->orderBy('kode')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Struktur Program');

        $sheet->fromArray(self::KOLOM, null, 'A1');
        $sheet->getStyle('A1:L1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8B1E30');

        $baris = 2;
        foreach ($subKegiatans as $sub) {
            $keg = $sub->kegiatan;
            $prog = $keg?->program;

            $sheet->fromArray([
                $prog?->urusan?->kode,
                $prog?->urusan?->nama,
                $prog?->opd?->nama,
                $prog?->kode,
                $prog?->nama,
                $prog?->sasaranPd?->nama,
                $keg?->kode,
                $keg?->nama,
                $sub->kode,
                $sub->nama,
                $sub->indikator,
                $sub->satuan,
            ], null, "A{$baris}");

            $baris++;
        }

        if ($subKegiatans->isEmpty()) {
            $sheet->setCellValue('A2', '(belum ada data — isi baris baru di bawah header untuk menambah struktur baru)');
        }

        foreach (range('A', 'L') as $kolom) {
            $sheet->getColumnDimension($kolom)->setAutoSize(true);
        }
        $sheet->getColumnDimension('H')->setWidth(40);
        $sheet->getColumnDimension('J')->setWidth(35);
        $sheet->freezePane('A2');

        $namaFile = 'template-struktur-program'.($opdId ? '-opd'.$opdId : '').'.xlsx';

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

        foreach (['opd', 'program nama', 'kegiatan nama', 'sub kegiatan nama'] as $wajib) {
            if (! array_key_exists($wajib, $kolomIndex)) {
                return $this->flashError("Kolom '{$wajib}' tidak ditemukan. Gunakan header sesuai template.");
            }
        }

        $berhasil = 0;
        $dilewati = [];
        $programBaru = 0;
        $kegiatanBaru = 0;
        $subBaru = 0;
        $nomorBaris = 1;

        foreach ($rows as $row) {
            $nomorBaris++;

            $ambil = function (string $nama) use ($kolomIndex, $row) {
                if (! isset($kolomIndex[$nama])) {
                    return null;
                }
                $nilai = $row[$kolomIndex[$nama]] ?? null;

                return $nilai === null ? null : trim((string) $nilai);
            };

            $opdNama       = $ambil('opd');
            $programNama   = $ambil('program nama');
            $kegiatanNama  = $ambil('kegiatan nama');
            $subNama       = $ambil('sub kegiatan nama');

            if (blank($opdNama) && blank($programNama) && blank($kegiatanNama) && blank($subNama)) {
                continue; // baris kosong, lewati diam-diam
            }

            if (blank($opdNama) || blank($programNama) || blank($kegiatanNama) || blank($subNama)) {
                $dilewati[] = "Baris {$nomorBaris}: OPD, Program Nama, Kegiatan Nama, dan Sub Kegiatan Nama wajib diisi semua.";
                continue;
            }

            $opd = Opd::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower($opdNama)])->first();
            if (! $opd) {
                $dilewati[] = "Baris {$nomorBaris}: OPD \"{$opdNama}\" tidak ditemukan. Tambahkan dulu lewat menu OPD (impor ini tidak membuat OPD baru).";
                continue;
            }

            try {
                DB::transaction(function () use (
                    $ambil, $opd, $programNama, $kegiatanNama, $subNama,
                    &$programBaru, &$kegiatanBaru, &$subBaru
                ) {
                    // --- Urusan (opsional dibuat otomatis kalau belum ada) ---
                    $urusanKode = $ambil('urusan kode');
                    $urusanNama = $ambil('urusan nama');
                    $urusan = null;

                    if (filled($urusanKode)) {
                        $urusan = Urusan::whereRaw('LOWER(TRIM(kode)) = ?', [strtolower($urusanKode)])->first();
                    }
                    if (! $urusan && filled($urusanNama)) {
                        $urusan = Urusan::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower($urusanNama)])->first();
                    }
                    if (! $urusan && filled($urusanKode) && filled($urusanNama)) {
                        $urusan = Urusan::create(['kode' => $urusanKode, 'nama' => $urusanNama]);
                    }

                    // --- Sasaran PD (opsional, best-effort match saja, tidak dibuat otomatis) ---
                    $sasaranNama = $ambil('sasaran pd');
                    $sasaran = filled($sasaranNama)
                        ? SasaranPd::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower($sasaranNama)])->first()
                        : null;

                    // --- Program: cari dalam lingkup Urusan+OPD, kalau tidak ada → buat ---
                    $programKode = $ambil('program kode');
                    $program = Program::where('opd_id', $opd->id)
                        ->when($urusan, fn ($q) => $q->where('urusan_id', $urusan->id))
                        ->where(function ($q) use ($programKode, $programNama) {
                            if (filled($programKode)) {
                                $q->whereRaw('LOWER(TRIM(kode)) = ?', [strtolower($programKode)]);
                            } else {
                                $q->whereRaw('LOWER(TRIM(nama)) = ?', [strtolower($programNama)]);
                            }
                        })
                        ->first();

                    if (! $program) {
                        $program = Program::create([
                            'kode' => $programKode,
                            'nama' => $programNama,
                            'urusan_id' => $urusan?->id,
                            'opd_id' => $opd->id,
                            'sasaran_pd_id' => $sasaran?->id,
                        ]);
                        $programBaru++;
                    } elseif ($program->nama !== $programNama) {
                        $program->update(['nama' => $programNama]);
                    }

                    // --- Kegiatan: cari dalam lingkup Program, kalau tidak ada → buat ---
                    $kegiatanKode = $ambil('kegiatan kode');
                    $kegiatan = Kegiatan::where('program_id', $program->id)
                        ->where(function ($q) use ($kegiatanKode, $kegiatanNama) {
                            if (filled($kegiatanKode)) {
                                $q->whereRaw('LOWER(TRIM(kode)) = ?', [strtolower($kegiatanKode)]);
                            } else {
                                $q->whereRaw('LOWER(TRIM(nama)) = ?', [strtolower($kegiatanNama)]);
                            }
                        })
                        ->first();

                    if (! $kegiatan) {
                        $kegiatan = Kegiatan::create([
                            'kode' => $kegiatanKode,
                            'nama' => $kegiatanNama,
                            'program_id' => $program->id,
                        ]);
                        $kegiatanBaru++;
                    } elseif ($kegiatan->nama !== $kegiatanNama) {
                        $kegiatan->update(['nama' => $kegiatanNama]);
                    }

                    // --- Sub Kegiatan: cari dalam lingkup Kegiatan, kalau tidak ada → buat ---
                    $subKode = $ambil('sub kegiatan kode');
                    $indikator = $ambil('indikator kinerja');
                    $satuan = $ambil('satuan');

                    $sub = SubKegiatan::where('kegiatan_id', $kegiatan->id)
                        ->where(function ($q) use ($subKode, $subNama) {
                            if (filled($subKode)) {
                                $q->whereRaw('LOWER(TRIM(kode)) = ?', [strtolower($subKode)]);
                            } else {
                                $q->whereRaw('LOWER(TRIM(nama)) = ?', [strtolower($subNama)]);
                            }
                        })
                        ->first();

                    if (! $sub) {
                        SubKegiatan::create([
                            'kode' => $subKode,
                            'nama' => $subNama,
                            'kegiatan_id' => $kegiatan->id,
                            'indikator' => $indikator,
                            'satuan' => $satuan,
                        ]);
                        $subBaru++;
                    } else {
                        $sub->update(array_filter([
                            'nama' => $subNama,
                            'indikator' => $indikator ?? $sub->indikator,
                            'satuan' => $satuan ?? $sub->satuan,
                        ], fn ($v) => $v !== null));
                    }
                });

                $berhasil++;
            } catch (\Throwable $e) {
                $dilewati[] = "Baris {$nomorBaris}: gagal disimpan ({$e->getMessage()}).";
            }
        }

        session(['import_hasil' => [
            'berhasil' => $berhasil,
            'dilewati' => $dilewati,
            'ringkasan' => "{$programBaru} Program baru, {$kegiatanBaru} Kegiatan baru, {$subBaru} Sub Kegiatan baru.",
        ]]);

        return redirect()->route('struktur.import.form')->with(
            'success',
            "{$berhasil} baris berhasil diproses.".(count($dilewati) ? ' '.count($dilewati).' baris perlu dicek.' : '')
        );
    }
}
