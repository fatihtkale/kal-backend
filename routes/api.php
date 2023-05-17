<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DataDirectoryController;
use App\Http\Controllers\Api\DirectoryController;
use App\Http\Controllers\Api\FieldsController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

// Auth
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('opgave/{token}', [TaskController::class, 'publicTask']);

Route::middleware(['apiUserAuth'])->group(function () {
    // Task
    Route::post('task', [TaskController::class, 'create']);
    Route::put('task/{id}', [TaskController::class, 'update']);
    Route::delete('task/{id}', [TaskController::class, 'delete']);
    Route::post('task/publish/{id}', [TaskController::class, 'setPublic']);
    Route::delete('task/publish/{id}', [TaskController::class, 'removePublic']);

    // Comments
    Route::get('comments/{task_id}', [CommentController::class, 'index']);
    Route::post('comment', [CommentController::class, 'create']);

    // Calendar
    Route::get('calendar/month', [CalendarController::class, 'getNowMonth']);
    Route::get('calendar/month/{month}', [CalendarController::class, 'getByMonth']);

    Route::get('calendar/week', [CalendarController::class, 'getNowWeek']);
    Route::get('calendar/week/{week}', [CalendarController::class, 'getByWeek']);

    Route::get('calendar/day', [CalendarController::class, 'getToday']);
    Route::get('calendar/day/{day}', [CalendarController::class, 'getByDay']);
});

Route::get('validate-token', function () {
    return ['error' => 'Token is valid'];
})->middleware('auth:api');


