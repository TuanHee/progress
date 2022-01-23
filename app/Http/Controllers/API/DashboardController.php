<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function recent()
    {
        $recent_projects = Project::with('joinedMembers')->whereHas('joinedMembers', function($query) {
            return $query->where('user_id', Auth::id());
        })
        ->withCount('joinedMembers', 'tasks', 'tasksCompleted')
        ->orderByDesc('updated_at')
        ->limit(4)
        ->get()
        ->map(function ($project) {
            return [
                'id'                    => $project->id,
                'title'                 => $project->title,
                'created_at'            => $project->updated_at->format('d-m-Y'),
                'updated_at'            => $project->updated_at->format('d-m-Y'),
                'tasks_count'           => $project->tasks_count,
                'tasks_completed_count' => $project->tasks_completed_count,
                'members' => $project->joinedMembers->map(function($members) {
                    return $members->user;
                }),
            ];
        });

        return response()->json($recent_projects);
    }

    public function assignedToMe()
    {
        $member_ids = ProjectMember::where('user_id', Auth::id())->get()->pluck('id');

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

        return response()->json($in_tasks);
    }
}
