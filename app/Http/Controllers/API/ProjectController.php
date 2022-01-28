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
        ->orderBy('created_at', 'DESC')
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
            return response()->json(['message' =>'Not Found'], 404);
        }

        if(! Gate::allows('view', [Project::class, $project])) {
            return response()->json(['message' =>'Not Found'], 404);
        }

        $project = Project::findOrFail($id);

        $project->load('taskLists', 'taskLists.tasks');

        return response()->json($project);
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

    // ------------------------------------------------------------------------- other
    public function author(Request $request, $id)
    {
        $project = Project::find($id);

        if ($project != null) {
            return response()->json([
                'user'  => $project->user,
            ]);
        }

        return response()->json(['message' =>'Not Found'], 404);
    }

    public function members(Request $request, $id)
    {
        $project = Project::find($id);

        if ($project != null) {
            return response()->json([
                'members'  => $project->members,
            ]);
        }
        return response()->json(['message' =>'Not Found'], 404);
    }
}
