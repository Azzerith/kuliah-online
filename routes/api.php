<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnnouncementController;

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

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // User routes
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::post('/users/{user}/promote-to-assistant', [UserController::class, 'promoteToAssistant']);

    // Program Studi routes
    Route::apiResource('program-studi', ProgramStudiController::class);

    // Course routes
    Route::apiResource('courses', CourseController::class);

    // Academic Period routes
    Route::apiResource('academic-periods', AcademicPeriodController::class);
    Route::post('/academic-periods/{academicPeriod}/activate', [AcademicPeriodController::class, 'activate']);

    // Class routes
    Route::apiResource('classes', ClassController::class);
    Route::post('/classes/join', [ClassController::class, 'join']);
    Route::post('/classes/{class}/add-assistant/{user}', [ClassController::class, 'addAssistant']);

    // Module routes
    Route::apiResource('classes.modules', ModuleController::class)->shallow();
    Route::post('/modules/{module}/upload-file', [ModuleController::class, 'uploadFile']);

    // Assignment routes
    Route::apiResource('classes.assignments', AssignmentController::class)->shallow();
    Route::post('/assignments/{assignment}/publish', [AssignmentController::class, 'publish']);
    Route::post('/assignments/{assignment}/questions', [AssignmentController::class, 'addQuestion']);

    // Submission routes
    Route::apiResource('assignments.submissions', SubmissionController::class)->shallow();
    Route::post('/assignments/{assignment}/submit', [SubmissionController::class, 'submit']);
    Route::post('/assignments/{assignment}/questions/{question}/answer', [SubmissionController::class, 'submitAnswer']);
    Route::post('/answers/{answer}/grade', [SubmissionController::class, 'gradeAnswer']);

    // Analytics routes
    Route::prefix('analytics')->group(function () {
        Route::get('/classes/{class}/performance', [AnalyticsController::class, 'classPerformance']);
        Route::get('/classes/{class}/students/{student}', [AnalyticsController::class, 'studentPerformance']);
    });

    // Todo routes
    Route::apiResource('classes.todos', TodoController::class)->shallow();
    Route::post('/todos/{todo}/progress', [TodoController::class, 'updateProgress']);
    Route::post('/progress/{progress}/verify', [TodoController::class, 'verifyProgress']);

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    });

    // Announcement routes
    Route::apiResource('announcements', AnnouncementController::class)->except(['index']);
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::post('/classes/{class}/announcements', [AnnouncementController::class, 'store']);
    Route::post('/announcements/{announcement}/mark-as-read', [AnnouncementController::class, 'markAsRead']);
});