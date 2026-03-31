<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ResultController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/questions', [QuestionController::class, 'index']);

Route::post('/quiz/start', [QuizController::class, 'start']);
Route::post('/quiz/answer', [QuizController::class, 'answer']);

Route::get('/results/{sessionId}', [ResultController::class, 'calculate']);

