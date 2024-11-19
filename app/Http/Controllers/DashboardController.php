<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $member_ids = ProjectMember::where('user_id', Auth::id())->get()->pluck('id');
        // DB::connection()->enableQueryLog();
        $in_tasks = Task::with('performers')->whereHas('performers', function($query) use ($member_ids) {
                return $query->whereIn('member_id', $member_ids);
            })
            ->where('completed', false)
            ->get()
            ->map(function ($task) {
                return [
                    'id'            => $task->id,
                    'title'         => $task->title,
                    'due_at'        => $task->due_at->format('d-m-Y'),
                    'due'           => now() > $task->due_at, //now() > $task->due_at && !$task->completed,
                ];
            });

        $in_projects = Project::with('joinedMembers')->whereHas('joinedMembers', function($query) {
                return $query->where('user_id', Auth::id());
            })
            ->withCount('joinedMembers', 'tasks', 'tasksCompleted')
            ->orderByDesc('updated_at')
            ->limit(4)
            ->get();
        // $queries = \DB::getQueryLog();
        // dd($queries);


        return Inertia::render('Dashboard', [
            'in_tasks'      => $in_tasks,
            'in_projects'    => $in_projects,
        ]);
    }
}
