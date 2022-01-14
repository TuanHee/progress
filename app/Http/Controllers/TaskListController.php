<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Redirect;

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

        return Response::json([
            'id'    => $taskList->id,
            'title' => $taskList->title,
            'tasks' => $taskList->tasks,
        ]);
    }

    public function update(Request $request, TaskList $taskList)
    {
        $this->validate($request, [
            'title'  => 'required'
        ]);

        $taskList->title = $request->post('title');
        $taskList->update();

        return Response::json([
            'taskList' => $taskList,
        ]);
    }

    public function destroy(TaskList $taskList)
    {
        if ($taskList->tasks()->count() > 0) {
            return back()->with('warning', 'Cannot delete, task records.');
        }

        $taskList->delete();

        return Redirect::back()->with('success', 'Task list deleted.');
    }
}
