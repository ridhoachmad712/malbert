<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AssessmentSetting;
use App\Models\Classroom;
use App\Models\LiteracyAssignment;
use App\Models\LiteracySubmission;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';
        $classrooms = Classroom::visibleTo($user);
        $assignments = LiteracyAssignment::visibleTo($user);
        $submissions = LiteracySubmission::visibleTo($user);
        $publications = Publication::query()->when(! $isAdmin, fn ($query) => $query->where('user_id', $user->id));

        return view('manage.dashboard', [
            'isAdmin' => $isAdmin,
            'assessmentConfigured' => AssessmentSetting::current() !== null,
            'stats' => [
                'classrooms' => (clone $classrooms)->count(),
                'activeAssignments' => (clone $assignments)->where('status', 'active')->count(),
                'pendingSubmissions' => (clone $submissions)->where('status', 'submitted')->count(),
                'unassessedSubmissions' => (clone $submissions)->where('status', 'approved')->whereDoesntHave('assessment')->count(),
                'pendingPublications' => (clone $publications)->where('review_status', 'pending')->count(),
            ],
            'recentAssignments' => (clone $assignments)->with('classroom')->latest('assigned_on')->limit(5)->get(),
            'submissionsToReview' => (clone $submissions)->where('status', 'submitted')->with(['student', 'assignment.classroom'])->latest('submitted_at')->limit(5)->get(),
            'submissionsToAssess' => (clone $submissions)->where('status', 'approved')->whereDoesntHave('assessment')->with(['student', 'assignment.classroom'])->latest('reviewed_at')->limit(5)->get(),
            'publicationsToReview' => $isAdmin
                ? (clone $publications)->where('review_status', 'pending')->with('author')->latest('updated_at')->limit(5)->get()
                : collect(),
            'recentPublications' => (clone $publications)->with('author')->latest('updated_at')->limit(5)->get(),
            'logs' => ActivityLog::when(! $isAdmin, fn ($query) => $query->where('user_id', $user->id))->latest()->limit(5)->get(),
        ]);
    }
}
