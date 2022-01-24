<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\TaskList;
use App\Notifications\CompleteRequest;
use App\Notifications\TaskAssigned;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function create(TaskList $taskList)
    {
        $project = $taskList->project;

        return Inertia::render('Project/Task/Create', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
            ],
            'members' => $project->members()->orderBy('created_at', 'asc')->get()->map->only(
                'id', 'name', 'profile_photo_url'
            ),
            'list' => [
                'id' => $taskList->id,
                'title' => $taskList->title,
            ],
            'priorities'  => Task::$priorities,
        ]);
    }

    public function store(Request $request, TaskList $taskList)
    {
        $this->validate($request, [
            'title' => 'required',
            'description'   => 'nullable',
            'start_date'    => 'required',
            'due_date'      => 'required',
        ]);

        $member_id = $taskList->project->members
            ->where('user_id', Auth::id())
            ->first()
            ->id;

        $task = new Task([
            'member_id'     => $member_id,
            'title'         => $request->input('title'),
            'description'   => $request->input('description'),
            'start_at'      => $request->input('start_date') ? Carbon::createFromFormat('Y-m-d', $request->input('start_date')) : null,
            'due_at'        => $request->input('due_date') ? Carbon::createFromFormat('Y-m-d', $request->input('due_date')) : null,
            'priority'      => $request->input('priority'),
        ]);

        $taskList->tasks()->save($task);
        $task->performers()->attach($request->input('members'));

        // $notify_users = ProjectMember::find($request->input('members'))->pluck('user');
        $notify_users = $task->performers()
                            ->get()
                            ->pluck('user')
                            ->where('id', '<>', Auth::id())
                            ->all();

        Notification::send($notify_users, new TaskAssigned($task));

        return Redirect::route('projects.show', [
            'project' => $taskList->project
        ])
        ->with('success', 'Task created.');
    }

    public function show(Task $task)
    {
        $project = Project::with('members')->find($task->taskList->project->id);

        $is_user_in = $project
            ->members
            ->pluck('user_id')
            ->contains(fn($value, $key) => $value == Auth::id());

        // if this user not in the project return 404
        if (!$is_user_in) {
            abort(404);
        }

        return Inertia::render('Project/Task/Show', [
            'project'   => $project,
            'members'   => $project->members()->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($member) {
                    return [
                        'id'            => $member->id,
                        'user_id'       => $member->user_id,
                        'project_id'    => $member->project_id,
                        'name'          => $member->name,
                        'email'         => $member->email,
                        'profile_photo_url' => $member->profile_photo_url,
                        'is_admin'      => $member->is_admin,
                        'created_at'    => $member->created_at->format('d-m-Y'),
                        'validated_at'  => optional($member->validated_at)->format('d-m-Y'),
                        'can' => [
                            'edit_member' => Auth::user()->can('update', [ProjectMember::class, $member])
                        ]
                    ];
                }),
            'can'   => [
                'update_task'  => Auth::user()->can('update', [Task::class, $task]),
                'delete_task'  => Auth::user()->can('delete', [Task::class, $task]),
                'create_attachment'  => Auth::user()->can('create', [Attachment::class, $project]),
            ],
            'task'  => [
                'id'    => $task->id,
                'title' => $task->title,
                'description'   => $task->description,
                'start_at'      => optional($task->start_at)->format('d-m-Y'),
                'due_at'        => optional($task->due_at)->format('d-m-Y'),
                'due'           => now() > $task->due_at && !$task->completed,
                'priority'      => $task->priority,
                'assigned_at'   => $task->assigned_at->format('d-m-Y'),
                // 'performers'    => $task->performers()->get()->map->only('id', 'name', 'profile_photo_url', 'pivot.request_complete'),
                'performers'    => $task->performers,
                'performers_request_complete' => $task->performers_request_complete,
                'list'          => $task->taskList,
                'completed'     => $task->completed,
                'attachments'   => $task->attachments()->get()->map(function($attachment) {
                    return [
                        'id'    => $attachment->id,
                        'type'  => $attachment->type,
                        'name'  => $attachment->name,
                        'link'  => $attachment->link,
                        'created_at' => $attachment->created_at->format('d-m-Y H:i'),
                        'can'   => [
                            'delete_attachment' => Auth::user()->can('delete', [Attachment::class, $attachment]),
                        ]
                    ];
                }),
                'comments'      => $task->comments()->with('member')->orderBy('created_at', 'desc')->get()->map(function($comment) {

                    $member = [
                        'id'    => $comment->member->id,
                        'user_id' => $comment->member->user_id,
                        'name'  => $comment->member->name,
                        'profile_photo_url' => $comment->member->profile_photo_url,
                    ];

                    if ($comment->trashed()) {
                        return [
                            'id'        => $comment->id,
                            'member'    => $member,
                            'status'   => 'deleted',
                            'duration'  => $comment->created_at->diffForHumans(),
                            'deleted'   => true,
                        ];
                    }

                    return [
                        'id'        => $comment->id,
                        'member'    => $member,
                        'content'   => $comment->content,
                        'duration'  => $comment->created_at->diffForHumans(),
                        'edited'    => $comment->created_at != $comment->updated_at,
                    ];
                }),
            ],
            'task_member_ids'       => $task->performers->pluck('pivot.member_id'),
            'current_member_id'     => optional($task->performers()->where('user_id', Auth::id())->first())->id,
            'requested_completion'     => $task->performers_request_complete()->where('user_id', Auth::id())->count() == 1,
        ]);
    }

    public function edit(Task $task)
    {
        if(! Gate::allows('update', [Task::class, $task])) {
            abort(403);
        }

        $project = Project::with([
            'members',
            'members.user',
        ])->find($task->taskList->project->id);

        return Inertia::render('Project/Task/Edit', [
            'project'   => $project,
            'members'   => $project->members()->orderBy('created_at', 'asc')
                ->get()->map->only(
                    'id', 'name', 'profile_photo_url'
                ),
            'task'      => [
                'id'    => $task->id,
                'title' => $task->title,
                'description'   => $task->description,
                'priority'      => $task->priority,
                'start_at'  => optional($task->start_at)->format('Y-m-d'),
                'due_at'    => optional($task->due_at)->format('Y-m-d'),
                'performers'    => $task->performers->pluck('id'),
            ],
            'priorities'    => Task::$priorities,
        ]);
    }

    public function update(Request $request, Task $task)
    {
        if(! Gate::allows('update', [Task::class, $task])) {
            abort(403);
        }

        $this->validate($request, [
            'title' => 'required',
            'description'   => 'nullable',
            'start_date'    => 'required',
            'due_date'      => 'required',
        ]);

        $task->update([
            'title'         => $request->get('title'),
            'description'   => $request->get('description'),
            'start_at'      => $request->get('start_date') ? Carbon::createFromFormat('Y-m-d', $request->get('start_date')) : null,
            'due_at'        => $request->get('due_date') ? Carbon::createFromFormat('Y-m-d', $request->get('due_date')) : null,
            'priority'      => $request->get('priority'),
        ]);

        $old = $task->performers->pluck('id');

        $task->performers()->sync($request->get('members'));

        // send notifications
        $notify_users = $task->performers()
                        ->get()
                        ->whereNotIn('id', $old)
                        ->pluck('user')
                        ->where('id', '<>', Auth::id())
                        ->all();

        Notification::send($notify_users, new TaskAssigned($task));

        return redirect()->route('tasks.show', [
            'task'  => $task->id,
        ])
        ->with('success', 'Task Updated');

    }

    public function destroy(Task $task)
    {
        $project_id = $task->taskList->project->id;
        $task->delete();

        return redirect()->route('projects.show', $project_id)
            ->with('success', 'Task Deleted.');
    }

    // ------------------------------------------------------------------------- other
    public function updateStatus(Request $request, Task $task)
    {
        $task->update($request->all());
        return $task;
    }

    public function requestComplete(Request $request, Task $task)
    {
        $user_id = Auth::id();
        $member_id = $task
            ->taskList
            ->project
            ->members
            ->where('user_id', $user_id)
            ->first()
            ->id;

        $task->performers()->updateExistingPivot($member_id, [
            'request_complete' => true,
        ]);

        $notify_users = $task
                        ->taskList
                        ->project
                        ->members
                        ->where('is_admin', true)
                        ->pluck('user');

        Notification::send($notify_users, new CompleteRequest($task));

        return redirect()->route('tasks.show', [
            'task'  => $task->id
        ]);
    }

}
