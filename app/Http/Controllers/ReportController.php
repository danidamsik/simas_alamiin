<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceReportExport;
use App\Models\Kelas;
use App\Models\Periode;
use App\Services\AttendanceReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(private readonly AttendanceReportService $reportService)
    {
    }

    public function index(Request $request): Response
    {
        $filters = $request->only('periode_id', 'kelas_id', 'tanggal_mulai', 'tanggal_selesai');
        $hasFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();
        $report = null;

        if ($hasFilters) {
            $validated = $this->validatedFilters($request);
            $filters = $validated;
            $report = $this->serializeReport($this->reportService->build($validated));
        }

        return Inertia::render('Laporan/Index', [
            'kelasOptions' => Kelas::query()->orderBy('nama_kelas')->get(['id', 'nama_kelas']),
            'periodeOptions' => Periode::query()
                ->orderByDesc('is_active')
                ->orderByDesc('id')
                ->get(['id', 'tahun_ajaran', 'semester', 'tanggal_mulai', 'tanggal_selesai', 'is_active'])
                ->map(fn (Periode $periode) => [
                    'id' => $periode->id,
                    'tahun_ajaran' => $periode->tahun_ajaran,
                    'semester' => $periode->semester,
                    'tanggal_mulai' => $periode->tanggal_mulai?->toDateString(),
                    'tanggal_selesai' => $periode->tanggal_selesai?->toDateString(),
                    'is_active' => $periode->is_active,
                ]),
            'filters' => $filters,
            'report' => $report,
            'hasFilters' => $hasFilters,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $report = $this->reportForExport($request);

        if ($report instanceof RedirectResponse) {
            return $report;
        }

        return Pdf::loadView('reports.attendance', ['report' => $report])
            ->setPaper('a4')
            ->download($this->filename($report, 'pdf'));
    }

    public function exportExcel(Request $request): BinaryFileResponse|RedirectResponse
    {
        $report = $this->reportForExport($request);

        if ($report instanceof RedirectResponse) {
            return $report;
        }

        return Excel::download(new AttendanceReportExport($report), $this->filename($report, 'xlsx'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedFilters(Request $request): array
    {
        $filters = $this->withDefaultPeriodDates($request->only('periode_id', 'kelas_id', 'tanggal_mulai', 'tanggal_selesai'));

        return Validator::make($filters, [
            'periode_id' => ['nullable', Rule::exists('periode', 'id')],
            'kelas_id' => ['required', Rule::exists('kelas', 'id')],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ])->validate();
    }

    /**
     * @return array<string, mixed>|RedirectResponse
     */
    private function reportForExport(Request $request): array|RedirectResponse
    {
        $report = $this->reportService->build($this->validatedFilters($request));

        if ($report['rows']->isEmpty()) {
            return redirect()
                ->route('laporan.index', $request->query())
                ->with('error', 'Laporan tidak dapat diekspor karena data absensi kosong.');
        }

        return $report;
    }

    /**
     * @param  array<string, mixed>  $report
     * @return array<string, mixed>
     */
    private function serializeReport(array $report): array
    {
        return [
            'periode' => $report['periode']?->only('id', 'tahun_ajaran', 'semester', 'is_active'),
            'periode_label' => $this->periodeLabel($report),
            'kelas' => $report['kelas']->only('id', 'nama_kelas'),
            'filters' => $report['filters'],
            'rows' => $report['rows']->values(),
            'totals' => $report['totals'],
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function filename(array $report, string $extension): string
    {
        $kelas = str($report['kelas']->nama_kelas)->replace([' ', '/'], '-')->lower();
        $periode = str($this->periodeLabel($report))->replace([' ', '/'], '-')->lower();

        return "laporan-absensi-{$kelas}-{$periode}.{$extension}";
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function periodeLabel(array $report): string
    {
        if ($report['periode']) {
            return $report['periode']->tahun_ajaran.' '.$report['periode']->semester;
        }

        return 'Semua Periode';
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function withDefaultPeriodDates(array $filters): array
    {
        if (! filled($filters['periode_id'] ?? null)) {
            return $filters;
        }

        $periode = Periode::query()->find($filters['periode_id']);

        if (! $periode) {
            return $filters;
        }

        if (! filled($filters['tanggal_mulai'] ?? null) && $periode->tanggal_mulai) {
            $filters['tanggal_mulai'] = $periode->tanggal_mulai->toDateString();
        }

        if (! filled($filters['tanggal_selesai'] ?? null) && $periode->tanggal_selesai) {
            $filters['tanggal_selesai'] = $periode->tanggal_selesai->toDateString();
        }

        return $filters;
    }
}
