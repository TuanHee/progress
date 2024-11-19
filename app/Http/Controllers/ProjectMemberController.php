<?php

namespace App\Http\Controllers;

use App\Mail\InviteMail;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Notifications\ProjectInvite;
use App\Traits\GeneratesTokenTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class ProjectMemberController extends Controller
{
    use GeneratesTokenTrait;

    // ------------------------------------------------------------------------- mail
    public function sendInviteMail(Request $request, Project $project)
    {
        $request->validate([
            'email'     => [
                'required',
                'email',
            ],
            'message'   => 'nullable',
        ]);
        $email = $request->get('email');
        // reject if user invite himself
        if (Auth::user()->email == $email) {
            return Redirect::route('projects.overview', [
                'project' => $project
            ])->with('warning', 'You can\'t invite yourself.');
        }
        // invited people already exists
        $count = $project->members()->where('email', $email)->count();
        if ($count != 0) {
            return Redirect::route('projects.overview', [
                'project' => $project
            ])->with('warning', 'This member has invited.');
        }
        // new project member
        $member = new ProjectMember();
        $member->email = $email;
        $member->invite_token = $this->GenerateToken();

        $user = User::where('email', $email)->first();

        if($user) {
            $member->user_id = $user->id;
            // send notification
            $user->notify(new ProjectInvite($member));
        }

        $project->members()->save($member);
        // send mail
        $data['project']    = $project->toArray();
        $data['member']     = $member->toArray();
        $data['inviter']    = Auth::user()->name;
        $data['message']    = $request->input('message');

        Mail::to($member->email)
            ->send(new InviteMail($data));

        return Redirect::route('projects.overview', [
            'project' => $project
        ])->with('success', 'Invitation Sent.');
    }

    public function deny(Request $request, ProjectMember $member)
    {
        $notification_id = $request->query('notification');
        Auth::user()->notifications()->find($notification_id)->markAsRead();

        return Redirect::back();
    }

    public function joinByMail(Request $request, ProjectMember $member)
    {
        if (Auth::user()->email == $member->email) {
            // update member id and validate member
            $member->update([
                'user_id'       => Auth::id(),
                'validated_at'  => now(),
                'invite_token'  => null,
            ]);

            if ($request->query('notification')) {
                $notification_id = $request->query('notification');
                Auth::user()->notifications()->find($notification_id)->markAsRead();
            }

            return Redirect::route('projects.show', ['project' => $member->project])
                ->with('success', 'You are joined to this project.');
        }

        return Redirect::route('dashboard')
            ->with('warning', 'Please use the invited email account.');
    }

    // ------------------------------------------------------------------------- link
    public function join(Project $project)
    {
        $member = $project->members()->where('user_id', Auth::id());

        if ($member->count() == 0) {
            $project->members()->create([
                'project_id'    => $project->id,
                'user_id'       => Auth::id(),
                'email'         => Auth::user()->email,
                'validated_at'  => now(),
            ]);

            return Redirect::route('projects.show', ['project' => $project])
                ->with('success', 'You are Joined to the project.');
        }

        // member invited
        $member = $member->first();

        if ($member->validated_at == null) {
            $member->validated_at = now();
            $member->update();

            return Redirect::route('projects.show', ['project' => $project])
                ->with('success', 'You are Joined to the project.');
        }

        return Redirect::route('projects.show', ['project' => $project])
            ->with('warning', 'You cannot join again because you are already in the project.');
    }

    public function updatePermission(Request $request, ProjectMember $member)
    {
        $member->is_admin = $request->post('setAdmin');
        $member->update();

        return Response::json([
            'is_admin' => $member->is_admin,
        ]);
    }

    public function remove(Project $project, ProjectMember $member)
    {
        if (Auth::id() != $member->user_id) {
            $member->delete();
        }

        return Redirect::route('projects.overview', [
            'project' => $project
        ])->with('success', 'Project member removed.');
    }
}
