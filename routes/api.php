<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TestAttemptController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InstitutionController;

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('student/login', [AuthController::class, 'studentLogin']);
    Route::post('admin/login', [AuthController::class, 'adminLogin']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
});

Route::get('departments', [DepartmentController::class, 'index']);
Route::get('institutions/all', [InstitutionController::class, 'all']);

Route::middleware('auth:sanctum')->group(function () {
    
    // institution routes
    Route::get('institutions', [InstitutionController::class, 'index']);
    Route::post('institutions', [InstitutionController::class, 'store']);
    Route::get('institutions/{id}', [InstitutionController::class, 'show']);
    Route::put('institutions/{id}', [InstitutionController::class, 'update']);
    Route::delete('institutions/{id}', [InstitutionController::class, 'destroy']);
    
    Route::apiResource('students', StudentController::class);
    Route::get('all-students', [StudentController::class, 'allStudents']);
    
    Route::get('tests', [TestController::class, 'index']);
    Route::post('tests', [TestController::class, 'store']);
    Route::get('tests/{id}', [TestController::class, 'show']);
    Route::put('tests/{id}', [TestController::class, 'update']);
    Route::delete('tests/{id}', [TestController::class, 'destroy']);
    Route::get('all-tests', [TestController::class, 'allTests']);
    
    Route::get('analytics/results', [AnalyticsController::class, 'results']);

    Route::post('questions/upload', [QuestionController::class, 'upload']);
    Route::post('questions/{testId}', [QuestionController::class, 'store']);
    Route::put('questions/{id}', [QuestionController::class, 'update']);
    Route::delete('questions/{id}', [QuestionController::class, 'destroy']);
    // Tests
    Route::post('tests/{testId}/start', [TestAttemptController::class, 'start']);
    Route::post('tests/submit', [TestAttemptController::class, 'submit']);
    Route::get('test-history', [TestAttemptController::class, 'studentTestHistory']);

    Route::get('test/result/{attemptId}', [TestResultController::class, 'testResult']);
});