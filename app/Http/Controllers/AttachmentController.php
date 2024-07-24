<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttachmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->validate($request, [
            'link' => [
                Rule::requiredIf($request->file('attachment') == null),
                'nullable',
                'url',
            ],
            'attachment' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,gif,bmp,tiff,webp,doc,docx,pdf,xls,xlsx,ppt,pptx,txt,csv,rtf,odt,ods,odp,odg,zip,rar,7z'
            ],
        ]);

        $link = $request->input('link') ?? null;
        $name = null;
        $type = "url";

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $link = $file->storePublicly('project_'. $task->taskList->project->id .'/task_'. $task->id, 'public');
            $name = $file->getClientOriginalName();
            $type = $file->extension();
        }

        $attachment = new Attachment([
            'link' => $link,
            'name' => $name,
            'type' => $type,
        ]);

        $task->attachments()->save($attachment);

        return redirect()->back()->with('success', 'Attachment added.');
    }

    public function destroy(Attachment $attachment)
    {
        $attachment->delete();

        return redirect()->back()->with('success', 'Attachment Deleted.');
    }
}
