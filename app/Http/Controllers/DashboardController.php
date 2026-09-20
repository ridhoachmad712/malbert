<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $query = Publication::query();
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }
        $stats = ['total' => (clone $query)->count(), 'published' => (clone $query)->where('status', 'published')->count(), 'pending' => (clone $query)->where('review_status', 'pending')->count(), 'draft' => (clone $query)->whereIn('review_status', ['draft', 'changes'])->where('status', '!=', 'archived')->count()];
        if ($request->input('status') === 'pending') {
            $query->where('review_status', 'pending');
        } elseif (in_array($request->input('status'), ['draft', 'published', 'archived'])) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('q')) {
            $query->where('content->title', 'like', '%'.$request->string('q').'%');
        }
        $logs = ActivityLog::when($request->user()->role !== 'admin', fn ($q) => $q->where('user_id', $request->user()->id))->latest()->limit(6)->get();

        return view('manage.dashboard', ['publications' => $query->with('author')->latest('updated_at')->paginate(12)->withQueryString(), 'stats' => $stats, 'logs' => $logs]);
    }
}
