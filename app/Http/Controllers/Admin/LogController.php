<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Log::with('user')
            ->latest()
            ->paginate(20);

        // dd($logs);

        return Inertia::render('Admin/Logs/Index', [
            'logs' => $logs,
        ]);
    }
}
