<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(ReportService $svc): Response
    {
        return Inertia::render('Reports/Index', [
            'report' => $svc->payload(chartDays: 7, topLimit: 5),
        ]);
    }

    public function custom(ReportService $svc): Response
    {
        return Inertia::render('Reports/Custom', [
            'report' => $svc->payload(chartDays: 30, topLimit: 10),
        ]);
    }
}
