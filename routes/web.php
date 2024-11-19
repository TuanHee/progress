<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return Redirect::route('dashboard');
    }
    return Redirect::route('home');
});

Route::get('/home', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ------------------------------------------------------------------------- projects
    Route::get('/projects/{project}/overview', [ProjectController::class, 'overview'])
        ->name('projects.overview');
    Route::post('/projects/{project}/registedEmailAddress', [ProjectController::class, 'registedEmailAddress'])
        ->name('projects.registedEmailAddress');
    Route::post('/projects/{project}/updateInviteLinkStatus', [ProjectController::class, 'updateInviteLinkStatus'])
        ->name('projects.updateInviteLinkStatus');
    Route::resource('projects', ProjectController::class)
        ->except('edit');

    // ------------------------------------------------------------------------- project members
    // ------------------------------------------------------------------------- mail
    Route::post('/projects/{project}/sendInviteMail', [ProjectMemberController::class, 'sendInviteMail'])
        ->name('projects.sendInviteMail');
    Route::get('/members/{member:invite_token}/invite', [ProjectMemberController::class, 'joinByMail'])
        ->name('members.joinByMail');

    // ------------------------------------------------------------------------- link
    Route::get('/projects/{project:invite_link_token}/invite', [ProjectMemberController::class, 'join'])
        ->name('project.invite-link');
    Route::post('/members/{member}/updatePermission', [ProjectMemberController::class, 'updatePermission'])
        ->name('members.updatePermission');
    Route::delete('/projects/{project}/members/{member}/remove', [ProjectMemberController::class, 'remove'])
        ->name('members.remove');

    // ------------------------------------------------------------------------- deny
    Route::delete('/members/{member:invite_token}/deny', [ProjectMemberController::class, 'deny'])
        ->name('members.deny');

    // ------------------------------------------------------------------------- project task lists
    Route::resource('projects.taskLists', TaskListController::class)
        ->only(['store', 'update', 'destroy'])
        ->shallow();

    // ------------------------------------------------------------------------- task
    Route::post('/tasks/{task}/updateStatus', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus');
    Route::post('/tasks/{task}/requestComplete', [TaskController::class, 'requestComplete'])
        ->name('tasks.requestComplete');
    Route::resource('taskLists.tasks', TaskController::class)
        ->shallow()
        ->except('index');

    // ------------------------------------------------------------------------- comments
    Route::resource('tasks.comments', CommentController::class)
        ->only(['store', 'update', 'destroy'])
        ->shallow();

    // ------------------------------------------------------------------------- comments
    Route::resource('tasks.attachments', AttachmentController::class)
        ->only(['store', 'destroy'])
        ->shallow();

    // ------------------------------------------------------------------------- notifications
    Route::delete('/notifications/markAsRead', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllAsRead');
});
