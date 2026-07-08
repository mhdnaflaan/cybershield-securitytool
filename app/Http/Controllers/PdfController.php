<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * Generate PDF report for a specific scan
     */
    public function generate($id)
    {
        $scan = Scan::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $resultData = $scan->result_data;

        $data = [
            'scan' => $scan,
            'result' => $resultData,
            'user' => auth()->user(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.report', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('cybershield-report-' . $scan->id . '.pdf');
    }

    /**
     * Generate PDF for the dashboard history (all scans)
     */
    public function generateAll()
    {
        $scans = Scan::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'scans' => $scans,
            'user' => auth()->user(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'total_scans' => $scans->count(),
        ];

        $pdf = Pdf::loadView('pdf.all-reports', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('cybershield-all-reports.pdf');
    }
}