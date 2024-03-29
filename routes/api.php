<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('change_password', [AuthController::class, 'changePassword']);

    Route::prefix('manager')->middleware(['role:Manager'])->group(function () {
        Route::get('list_employees' , [AuthController::class, 'listEmployees']);
        Route::post('/tasks', [TaskController::class, 'addTask']);
        Route::put('/tasks/{id}', [TaskController::class, 'updateTask']);
        Route::post('/tasks/assign', [TaskController::class, 'assignTask']);
        Route::get('/tasks', [TaskController::class, 'listAllTasks']);
        Route::get('/tasks/{id}' , [TaskController::class , 'showTask']);
    });


    Route::prefix('employee')->middleware(['role:Employee'])->group(function () {
        Route::get('/tasks', [TaskController::class, 'listEmployeeTasks']);
        Route::put('/tasks/status/{id}', [TaskController::class, 'updateTaskStatus']);
    });
});

