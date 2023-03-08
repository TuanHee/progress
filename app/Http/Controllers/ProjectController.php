<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Traits\GeneratesTokenTrait;
use DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectController extends Controller
{
    use GeneratesTokenTrait;

    public function index(Request $request)
    {
        // DB::connection()->enableQueryLog();
        $projects = Project::with([
                'user',
                'joinedMembers',
            ])
            ->where('user_id', Auth::id())
            ->orWhereHas('joinedMembers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'DESC')
            ->applyFilters($request->only('search'))
            ->withCount('joinedMembers', 'tasks', 'tasksCompleted')
            ->paginate(10);

        // $queries = \DB::getQueryLog();
        // dd($queries);
        return Inertia::render('Project/Index', [
            'filter'  => $request->all(),
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        return Inertia::render('Project/Create');
    }

    public function store(Request $request)
    {
        $project = new Project(
            $request->validate([
                'title' => [
                    'required',
                    Rule::unique('projects')->where(function ($query) {
                        return $query->where('user_id', Auth::id());
                    }),
                ],
                'description' => 'nullable',
            ])
        );

        Auth::user()->projects()->save($project);
        // add user as admin in the project
        $project->members()->save(new ProjectMember([
            'user_id'   => Auth::user()->id,
            'email'     => Auth::user()->email,
            'project_id'=> $project->id,
            'is_admin'  => true,
            'validated_at'  => now(),
        ]));

        return Redirect::route('projects.show', ['project' => $project])
            ->with('success', 'Project created.');
    }

    public function show(Project $project)
    {
        if(! Gate::allows('view', [Project::class, $project])) {
            abort(404);
        }

        return Inertia::render('Project/Show', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
            ],
            'members' => $project->members()->orderBy('created_at', 'asc')->get()->map->only('name', 'profile_photo_url'),
            'can'   => [
                'create_task_list' => Auth::user()->can('create', [TaskList::class, $project]),
                'create_task' => Auth::user()->can('create', [Task::class, $project]),
            ],
            'taskLists' => $project->taskLists()
            ->get()
            ->map(function ($list) {
                return [
                    'id'    => $list->id,
                    'title' => $list->title,
                    'tasks' => $list->tasks()
                        ->get()
                        ->map(function($task) {
                            return [
                                'id'            => $task->id,
                                'title'         => $task->title,
                                'priority'      => $task->priority,
                                'assigned_at'   => $task->assigned_at->format('d-m-Y'),
                                'start_at'      => $task->start_at->format('d-m-Y'),
                                'due_at'        => $task->due_at->format('d-m-Y'),
                                'due'           => now() > $task->due_at, //now() > $task->due_at && !$task->completed,
                                'performers'    => $task->performers->map->only('name', 'profile_photo_url'),
                                'completed'     => $task->completed,
                                'comments_count'=> $task->comments->count(),
                            ];
                        })
                ];
            })
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $this->validate($request, [
            'title' => [
                'required',
                Rule::unique('projects')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($project->id),
            ],
            'description' => 'nullable',
        ]);

        $project->title = $request->input('title');
        $project->description = $request->input('description');
        $project->update();

        return Redirect::route('projects.overview', ['project' => $project])
                ->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        //
    }

    // ------------------------------------------------------------------------- other
    public function overview(Project $project)
    {
        if(! Gate::allows('view', [Project::class, $project])) {
            abort(404);
        }

        $project = $project
            ->with([
                'user',
                'members',
            ])
            ->where('id', $project->id)->first();

        return Inertia::render('Project/Overview', [
            'can'   => [
                'update_project' => Auth::user()->can('update', [Project::class, $project]),
                'create_project_member' => Auth::user()->can('create', [ProjectMember::class, $project]),
            ],
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'invite_link' => $project->invite_link,
                'invite_link_status' => $project->invite_link_status,
                'user' => $project->user->makeVisible(['id', 'name', 'email', 'profile_photo_url']),
            ],
            'members' => $project->members()
                ->orderBy('created_at', 'asc')
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
        ]);
    }

    public function registedEmailAddress(Request $request, Project $project)
    {
        $members_in_project = ProjectMember::where('project_id', $project->id)
                                ->pluck('user_id');

        return User::where('email', 'like', "%$request->keyword%")
            ->where('email', '!=', Auth::user()->email)
            ->whereNotIn('id', $members_in_project)
            ->get(['name', 'email']);
    }

    public function updateInviteLinkStatus(Request $request, Project $project)
    {
        $project->invite_link_status = $request->post('invite_link_status');

        if ($project->invite_link_status) {
            $project->invite_link_token = $this->GenerateToken();
        } else {
            $project->invite_link_token = null;
        }

        $project->update();

        return Response::json([
            'invite_link_enable' => $project->invite_link_status,
            'link'  => $project->invite_link,
        ]);
    }
}
