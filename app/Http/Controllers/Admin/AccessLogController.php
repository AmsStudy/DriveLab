<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use Illuminate\Http\Request;

class AccessLogController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = AccessLog::with('soal');

        // Admin hanya melihat log soal miliknya
        if (!$user->isSuperAdmin()) {
            $query->whereHas('soal', fn($q) => $q->where('user_id', $user->id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('accessed_at', $request->date);
        }

        if ($request->filled('soal_id')) {
            $query->where('soal_id', $request->soal_id);
        }

        $logs = $query->latest('accessed_at')->paginate(30);

        return view('admin.logs.index', compact('logs'));
    }
}
