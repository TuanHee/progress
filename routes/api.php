<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\TaskListController;
use App\Http\Controllers\API\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // ------------------------------------------------------------------------- user
    // get auth use data
    Route::get('/user', [UserController::class, 'user']);

    // update password
    Route::put('/user/updatePassword', [UserController::class, 'updatePassword']);

    // logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // ------------------------------------------------------------------------- dashboard
    Route::get('/projects/recent', [DashboardController::class, 'recent']);
    Route::get('tasks/assignedToMe', [DashboardController::class, 'assignedToMe']);

    // ------------------------------------------------------------------------- projects
    Route::get('/projects/{project}/author', [ProjectController::class, 'author']);
    Route::get('/projects/{project}/members', [ProjectController::class, 'members']);
    Route::apiResource('projects', ProjectController::class);

    Route::apiResource('tasks', TaskController::class)->except('index');
    // ------------------------------------------------------------------------- projects
    Route::apiResource('projects.taskLists', TaskListController::class)
        ->shallow()
        ->except('index', 'show');
});
