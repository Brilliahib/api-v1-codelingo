<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LearningPathController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserSectionProgressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes for users (requires authentication)
Route::middleware('auth:api')->group(function () {
    Route::get('/auth/get-auth', [AuthController::class, 'getAuth']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::post('/auth/update-account', [AuthController::class, 'updateAccount']);
    // Learning Path routes
    Route::prefix('learning-paths')->group(function () {
        Route::get('/', [LearningPathController::class, 'index']);
        Route::post('/', [LearningPathController::class, 'store'])->middleware('role:admin');
        Route::get('/{id}', [LearningPathController::class, 'show']);
        Route::put('/{id}', [LearningPathController::class, 'update'])->middleware('role:admin');
        Route::delete('/{id}', [LearningPathController::class, 'destroy'])->middleware('role:admin');
    });

    // Material routes
    Route::prefix('materials')->group(function () {
        Route::get('/{learningPathId}', [MaterialController::class, 'index']);
        Route::post('/', [MaterialController::class, 'store'])->middleware('role:admin');
        Route::get('/detail/{id}', [MaterialController::class, 'show']);
        Route::put('/{id}', [MaterialController::class, 'update'])->middleware('role:admin');
        Route::delete('/{id}', [MaterialController::class, 'destroy'])->middleware('role:admin');
    });

    // Quiz routes
    Route::prefix('quizzes')->group(function () {
        Route::get('/{learningPathId}', [QuizController::class, 'index']);
        Route::post('/', [QuizController::class, 'store'])->middleware('role:admin');
        Route::get('/detail/{id}', [QuizController::class, 'show']);
        Route::put('/{id}', [QuizController::class, 'update'])->middleware('role:admin');
        Route::delete('/{id}', [QuizController::class, 'destroy'])->middleware('role:admin');
    });

    // Section routes
    Route::prefix('sections')->group(function () {
        Route::get('/{materialId}', [SectionController::class, 'index']);
        Route::post('/', [SectionController::class, 'store'])->middleware('role:admin');
        Route::get('/detail/{id}', [SectionController::class, 'show']);
        Route::put('/{id}', [SectionController::class, 'update'])->middleware('role:admin');
        Route::delete('/{id}', [SectionController::class, 'destroy'])->middleware('role:admin');
    });

    Route::prefix('questions')->group(function () {
        Route::get('/{quizId}', [QuestionController::class, 'index']);
        Route::post('/', [QuestionController::class, 'store']);
        Route::get('/detail/{id}', [QuestionController::class, 'show']);
        Route::put('/{id}', [QuestionController::class, 'update']);
        Route::delete('/{id}', [QuestionController::class, 'destroy']);
        Route::post('/submit/{questionId}', [QuestionController::class, 'submitSingleQuestion']);
    });    

    // User Section Progress routes
    Route::prefix('progress')->group(function () {
        Route::get('/{userId}', [UserSectionProgressController::class, 'index']);
        Route::post('/complete', [UserSectionProgressController::class, 'complete']);
    });

    Route::prefix('leaderboard')->group(function () {
        Route::get('/', [LeaderboardController::class, 'getTopUsers']);
    });
});
