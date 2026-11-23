<?php

namespace App\Http\Controllers;

use App\Exports\Form81ExcelExporter;
use App\Models\Opd;
use App\Services\Form81DataService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;
class Form81Controller extends Controller
{
    public function __construct(
        private readonly Form81DataService $dataService,
        private readonly Form81ExcelExporter $excelExporter,
    ) {}

    public function index(Request $request): View
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $opdId = $request->input('opd_id');

        // Batasi operator hanya OPD-nya sendiri
        $user = auth()->user();
        if ($user->role === 'operator_opd') {
            $opdId = $user->opd_id;
        }

        $data    = $this->dataService->build($tahun, $opdId ? (int) $opdId : null);
        $opdList = Opd::orderBy('nama')->get();

        return view('form81.index', compact('data', 'tahun', 'opdId', 'opdList'));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $opdId = $request->input('opd_id');

        $user = auth()->user();
        if ($user->role === 'operator_opd') {
            $opdId = $user->opd_id;
        }

        $data = $this->dataService->build($tahun, $opdId ? (int) $opdId : null);
        $path = $this->excelExporter->export($data);

        $filename = 'Form-81_' . str_replace(' ', '-', $data['header']['opd']) . '_' . $tahun . '.xlsx';

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf(Request $request): Response
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $opdId = $request->input('opd_id');

        $user = auth()->user();
        if ($user->role === 'operator_opd') {
            $opdId = $user->opd_id;
        }

        $data = $this->dataService->build($tahun, $opdId ? (int) $opdId : null);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('form81.pdf', ['data' => $data])
            ->setPaper('a3', 'landscape')
            ->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);

        $filename = 'Form-81_' . str_replace(' ', '-', $data['header']['opd']) . '_' . $tahun . '.pdf';

        return $pdf->download($filename);
    }
}