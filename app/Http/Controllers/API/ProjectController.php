<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects = Project::select('id', 'title', 'user_id', 'created_at', 'updated_at')
        ->with([
            'user:id,name',
            'joinedMembers:id,user_id,project_id',
        ])
        ->where('user_id', $request->user()->id)
        ->orWhereHas('joinedMembers', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })
        ->withCount('joinedMembers')
        ->get()
        ->map(function ($project) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'created_at' => $project->created_at->format('d-m-Y'),
                'updated_at' => $project->updated_at->format('d-m-Y'),
                'joinedMembers' => $project->joinedMembers->map(function($members) {
                    return $members->user;
                }),
            ];
        });

        // $projects = Project::select('id', 'title')->where('user_id', $request->user()->id)
        // ->orWhereHas('joinedMembers', function ($query) use ($request) {
        //     $query->where('user_id', $request->user()->id);
        // })
        // ->get();

        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

        $project->members()->save(new ProjectMember([
            'user_id'   => Auth::user()->id,
            'email'     => Auth::user()->email,
            'project_id'=> $project->id,
            'is_admin'  => true,
            'validated_at'  => now(),
        ]));

        return response()->json($project);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = Project::find($id);

        if($project == null) {
            abort(404, 'Not Found');
        }

        if(! Gate::allows('view', [Project::class, $project])) {
            abort(404, 'Not Found');
        }

        $project = Project::findOrFail($id);

        $project->load('taskLists', 'taskLists.tasks');

        return response()->json( $project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // ------------------------------------------------------------------------- other
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
                'joinedMembers' => $project->joinedMembers->map(function($members) {
                    return $members->user;
                }),
            ];
        });

        return response()->json($recent_projects);
    }
}
