<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskListController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $title = $request->post('title');

        $taskList = new TaskList([
            'title'  => $title,
        ]);

        $project->taskLists()->save($taskList);

        return response()->json([
            'id'    => $taskList->id,
            'title' => $taskList->title,
            'tasks' => $taskList->tasks,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $taskList = TaskList::find($id);

        if ($taskList != null) {
            $taskList->title = $request->input('title');
            $taskList->update();

            return response()->json(['message'  => 'Task List updated']);
        }

        return response()->json(['message' =>'Not Found'], 404);
    }

    public function destroy($id)
    {
        $taskList = TaskList::find($id);

        if ($taskList == null) {
            return response()->json([
                'message'   => 'Not Found',
            ], 404);
        }

        if ($taskList->tasks->count() > 0) {
            return response()->json(['warning' => 'Cannot delete, task records.'], 202);
        }

        $taskList->delete();

        return response()->json(['success' => 'Task list deleted.']);
    }
}
