<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Form81ExcelExporter
{
    public function export(array $data): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Form 81');

        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3);
        $sheet->getPageMargins()->setTop(0.5)->setRight(0.4)->setBottom(0.4)->setLeft(0.4);

        // ============ HEADER JUDUL ============
        $sheet->setCellValue('A1', 'EVALUASI TERHADAP HASIL RENCANA KERJA SKPD');
        $sheet->setCellValue('A2', 'PEMERINTAH KABUPATEN NIAS BARAT — TAHUN ' . $data['header']['tahun']);
        $sheet->setCellValue('A3', 'SKPD: ' . strtoupper($data['header']['opd']));

        $sheet->mergeCells('A1:T1');
        $sheet->mergeCells('A2:T2');
        $sheet->mergeCells('A3:T3');
        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ============ HEADER TABEL (2 baris) ============
        $startRow = 5;
        $headerA  = [
            'A' => ['No', 2],
            'B' => ['Kode', 2],
            'C' => ['Urusan/Bidang Urusan/Program/Kegiatan/Sub Kegiatan', 2],
            'D' => ['Indikator Kinerja Program/Kegiatan', 2],
            'E' => ['Satuan', 2],
            'F' => ['Target Renja SKPD Tahun ' . $data['header']['tahun'], null], // merge kolom
            'H' => ['Realisasi Kinerja pada Triwulan', null],
            'P' => ['Realisasi Capaian Kinerja & Anggaran s/d Tahun Berjalan', null],
            'R' => ['Tingkat Capaian (%)', null],
            'S' => ['Unit SKPD Penanggung Jawab', 2],
            'T' => ['Ket.', 2],
        ];

        // Baris pertama header
        foreach ($headerA as $col => [$label, $rowspan]) {
            $sheet->setCellValue($col . $startRow, $label);
        }

        // Merge kolom yang punya sub-header (baris 5+6)
        $sheet->mergeCells("A{$startRow}:A" . ($startRow + 1));
        $sheet->mergeCells("B{$startRow}:B" . ($startRow + 1));
        $sheet->mergeCells("C{$startRow}:C" . ($startRow + 1));
        $sheet->mergeCells("D{$startRow}:D" . ($startRow + 1));
        $sheet->mergeCells("E{$startRow}:E" . ($startRow + 1));
        $sheet->mergeCells("F{$startRow}:G{$startRow}"); // Target K, Rp
        $sheet->mergeCells("H{$startRow}:O{$startRow}"); // TW I..IV masing-masing K, Rp = 8 kolom
        $sheet->mergeCells("P{$startRow}:Q{$startRow}"); // Realisasi K, Rp
        $sheet->mergeCells("R{$startRow}:R" . ($startRow + 1));
        $sheet->mergeCells("S{$startRow}:S" . ($startRow + 1));
        $sheet->mergeCells("T{$startRow}:T" . ($startRow + 1));

        // Baris kedua header (sub-kolom K/Rp)
        $sub = $startRow + 1;
        $sheet->setCellValue("F{$sub}", 'K');
        $sheet->setCellValue("G{$sub}", 'Rp');
        $sheet->setCellValue("H{$sub}", 'TW I K');
        $sheet->setCellValue("I{$sub}", 'TW I Rp');
        $sheet->setCellValue("J{$sub}", 'TW II K');
        $sheet->setCellValue("K{$sub}", 'TW II Rp');
        $sheet->setCellValue("L{$sub}", 'TW III K');
        $sheet->setCellValue("M{$sub}", 'TW III Rp');
        $sheet->setCellValue("N{$sub}", 'TW IV K');
        $sheet->setCellValue("O{$sub}", 'TW IV Rp');
        $sheet->setCellValue("P{$sub}", 'K');
        $sheet->setCellValue("Q{$sub}", 'Rp');

        // Style header
        $sheet->getStyle("A{$startRow}:T" . ($startRow + 1))
            ->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A{$startRow}:T" . ($startRow + 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getStyle("A{$startRow}:T" . ($startRow + 1))
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8E8E8');

        // ============ DATA ROWS ============
        $row = $startRow + 2;

        foreach ($data['rows'] as $item) {
            $sheet->setCellValue("A{$row}", $item['level'] === 'subkegiatan' ? $item['no'] : '');
            $sheet->setCellValue("B{$row}", $item['kode']);
            $sheet->setCellValue("C{$row}", $this->indent($item['level']) . $item['uraian']);
            $sheet->setCellValue("D{$row}", $item['indikator']);
            $sheet->setCellValue("E{$row}", $item['satuan']);
            $sheet->setCellValue("F{$row}", $item['target_k']);
            $sheet->setCellValue("G{$row}", $item['target_rp']);
            $sheet->setCellValue("H{$row}", $item['tw_1_k']);
            $sheet->setCellValue("I{$row}", $item['tw_1_rp']);
            $sheet->setCellValue("J{$row}", $item['tw_2_k']);
            $sheet->setCellValue("K{$row}", $item['tw_2_rp']);
            $sheet->setCellValue("L{$row}", $item['tw_3_k']);
            $sheet->setCellValue("M{$row}", $item['tw_3_rp']);
            $sheet->setCellValue("N{$row}", $item['tw_4_k']);
            $sheet->setCellValue("O{$row}", $item['tw_4_rp']);
            $sheet->setCellValue("P{$row}", $item['real_k']);
            $sheet->setCellValue("Q{$row}", $item['real_rp']);
            $sheet->setCellValue("R{$row}", $item['pct_k']);
            $sheet->setCellValue("S{$row}", $item['opd']);
            $sheet->setCellValue("T{$row}", $item['ket']);

            // Warnai baris hierarki
            if (in_array($item['level'], ['urusan', 'program', 'kegiatan'], true)) {
                $rgb = ['urusan' => '761D2E', 'program' => 'C72C3D', 'kegiatan' => 'F2A7AD'][$item['level']];
                $sheet->getStyle("A{$row}:T{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($rgb);
                $sheet->getStyle("A{$row}:T{$row}")->getFont()
                    ->setBold(true)
                    ->getColor()->setRGB($item['level'] === 'kegiatan' ? '000000' : 'FFFFFF');
            }

            $row++;
        }

        // ============ FOOTER TOTAL ============
        $sheet->setCellValue("C{$row}", 'TOTAL');
        $sheet->setCellValue("G{$row}", $data['footer']['total_target_rp']);
        $sheet->setCellValue("Q{$row}", $data['footer']['total_real_rp']);
        $sheet->setCellValue("R{$row}", $data['footer']['rata_pct_rp']);
        $sheet->getStyle("A{$row}:T{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:T{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FDF3F4');

        // ============ FORMAT & BORDER ============
        $lastRow = $row;
        $sheet->getStyle("A{$startRow}:T{$lastRow}")->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Format angka Rp (kolom G, I, K, M, O, Q) — ribuan pakai titik
        foreach (['G', 'I', 'K', 'M', 'O', 'Q'] as $col) {
            $sheet->getStyle("{$col}" . ($startRow + 2) . ":{$col}{$lastRow}")
                ->getNumberFormat()->setFormatCode('#,##0');
        }
        // Format persentase (kolom R)
        $sheet->getStyle("R" . ($startRow + 2) . ":R{$lastRow}")
            ->getNumberFormat()->setFormatCode('0.00"%"');

        // Lebar kolom
        $widths = [
            'A' => 5, 'B' => 15, 'C' => 45, 'D' => 25, 'E' => 10,
            'F' => 10, 'G' => 15,
            'H' => 8, 'I' => 13, 'J' => 8, 'K' => 13, 'L' => 8, 'M' => 13, 'N' => 8, 'O' => 13,
            'P' => 10, 'Q' => 15,
            'R' => 10, 'S' => 25, 'T' => 15,
        ];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
        $sheet->getRowDimension($startRow)->setRowHeight(28);
        $sheet->getRowDimension($startRow + 1)->setRowHeight(24);

        // Freeze header
        $sheet->freezePane("A" . ($startRow + 2));

        // ============ TANDA TANGAN ============
        $signRow = $lastRow + 3;
        $sheet->setCellValue("N{$signRow}", 'Nias Barat, ' . now()->translatedFormat('d F Y'));
        $sheet->setCellValue("N" . ($signRow + 1), 'Kepala SKPD');
        $sheet->setCellValue("N" . ($signRow + 5), '(_______________________)');
        $sheet->setCellValue("N" . ($signRow + 6), 'NIP.');
        $sheet->getStyle("N{$signRow}:N" . ($signRow + 6))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ============ SIMPAN KE FILE TEMP ============
        $path = storage_path('app/tmp/form81_' . now()->format('YmdHis') . '_' . uniqid() . '.xlsx');
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }

    private function indent(string $level): string
    {
        return match ($level) {
            'urusan'      => '',
            'program'     => '   ',
            'kegiatan'    => '      ',
            'subkegiatan' => '         ',
            default       => '',
        };
    }
}