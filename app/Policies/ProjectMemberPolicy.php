<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectMemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProjectMember  $projectMember
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ProjectMember $projectMember)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Project $project)
    {
        return $project->members->where('user_id', $user->id)->first()->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProjectMember  $projectMember
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ProjectMember $projectMember)
    {
        $project = $projectMember->project()->first();

        return $project->members->where('user_id', $user->id)->first()->is_admin &&
            $user->id != $projectMember->user_id &&
            $project->user_id != $projectMember->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProjectMember  $projectMember
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ProjectMember $projectMember)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProjectMember  $projectMember
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ProjectMember $projectMember)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProjectMember  $projectMember
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ProjectMember $projectMember)
    {
        //
    }
}
