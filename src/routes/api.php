<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// user
Route::get('/boards', [UserController::class, 'getBoards'])->name('user.getBoards');
Route::post('/board/create', [UserController::class, 'createBoard'])->name('user.createBoard');

// board
Route::get('/board/{board}', [BoardController::class, 'show'])->name('board.show');
Route::put('/board/{board}', [BoardController::class, 'update'])->name('board.update');
Route::delete('/board/{board}', [BoardController::class, 'destroy'])->name('board.destroy');
Route::get('/board/{board}/tasks', [BoardController::class, 'getTasks'])->name('board.getTasks');
Route::post('/board/{board}/task/create', [BoardController::class, 'createTask'])->name('board.createTask');
Route::get('/board/{board}/trash', [BoardController::class, 'getTrashedTasks'])->name('board.getTrashedTasks');

// task
Route::get('/board/{board}/task/{task}', [TaskController::class, 'show'])->name('task.show');
Route::put('/board/{board}/task/{task}', [TaskController::class, 'update'])->name('task.update');
Route::delete('/board/{board}/task/{task}', [TaskController::class, 'destroy'])->name('task.destroy');
Route::get('/board/{board}/trash/task/{task}', [TaskController::class, 'showTrashed'])->withTrashed()->name('task.showTrashed');
Route::patch('/board/{board}/trash/task/{task}/restore', [TaskController::class, 'restore'])->withTrashed()->name('task.restore');