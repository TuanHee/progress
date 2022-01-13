<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\ProjectMember;
use App\Models\Task;
use Auth;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function store(Request $request, Task $task)
    {
        $request->validate([
            'content'   => 'required',
        ]);
        // get member id
        $member_id = ProjectMember::where('user_id', Auth::id())
                        ->where('project_id', $task->taskList->project_id)
                        ->first()
                        ->id;

        $task->comments()->create([
            'content'   => $request->get('content'),
            'member_id' => $member_id,
        ]);

        return redirect()->route('tasks.show', [
            'task'  => $task->id
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        $comment->update($request->validate([
            'content' => 'required'
        ]));

        return redirect()->route('tasks.show', $comment->task->id);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back();
    }
}
