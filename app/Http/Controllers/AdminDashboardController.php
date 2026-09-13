<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(private ReportService $reports)
    {
    }

    public function index(Request $request): View
    {
        [$from, $to] = $this->period($request);

        return view('admin.dashboard', [
            'summary' => $this->reports->summary($from, $to),
            'ordersByStatus' => $this->reports->ordersByStatus($from, $to),
            'from' => $from?->format('Y-m-d'),
            'to' => $to?->format('Y-m-d'),
        ]);
    }

    public function reports(Request $request): View
    {
        [$from, $to] = $this->period($request);

        return view('admin.reports', [
            'summary' => $this->reports->summary($from, $to),
            'ordersByStatus' => $this->reports->ordersByStatus($from, $to),
            'topMedicines' => $this->reports->topMedicines($from, $to),
            'walletMovement' => $this->reports->walletMovement($from, $to),
            'from' => $from?->format('Y-m-d'),
            'to' => $to?->format('Y-m-d'),
        ]);
    }

    private function period(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return [
            isset($validated['from']) ? Carbon::parse($validated['from'])->startOfDay() : null,
            isset($validated['to']) ? Carbon::parse($validated['to'])->endOfDay() : null,
        ];
    }
}