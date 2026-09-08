<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $activities = ActivityLog::with('user')
            ->latest('created_at')
            ->paginate(25);

        return view('admin.activity.index', compact('activities'));
    }
}
