<?php

use App\Http\Controllers\AlarmController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\CampainController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\OTPCodeController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SMSTemplateController;
use App\Http\Controllers\SurvayController;
use App\Http\Controllers\SurvayQuestionController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\TreatmentPlanController;
use App\Http\Controllers\TreatmentServiceController;
use App\Http\Controllers\TreatmentServiceOptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('login', 'login')->withoutMiddleware('auth:api');
        Route::get('me', 'me');
        Route::post('refresh', 'refresh');
        Route::post('logout', 'logout');
        Route::post('change-role', 'changeRole');

    });

    Route::controller(OTPCodeController::class)->prefix('otp-code')->withoutMiddleware('auth:api')->group(function () {
        Route::post('generate', 'generate');
        Route::post('verify', 'verify');
    });

    Route::controller(PasswordController::class)->prefix('password')->group(function () {
        Route::post('reset', 'reset');
        Route::post('change', 'change');
    });

    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
        Route::get('charts', 'charts');
    });

    Route::controller(ConsultantController::class)->prefix('consultants')->group(function () {
        Route::get('', 'index');
    });

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('/{user}', 'update');
        Route::delete('/{user}', 'destroy');
    });

    Route::get('roles', [RoleController::class, 'index']);

    Route::controller(PatientController::class)->prefix('patients')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::get('/{patient}', 'show');
        Route::patch('/{patient}', 'update');
        Route::delete('/{patient}', 'destroy');
    });

    Route::get('alarms', [AlarmController::class, 'index']);
    Route::get('holidays', [HolidayController::class, 'index']);
    Route::get('provinces', [ProvinceController::class, 'index']);
    Route::get('cities', [CityController::class, 'index']);
    Route::get('lead-sources', [LeadSourceController::class, 'index']);

    Route::controller(CallController::class)->prefix('calls')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
    });

    Route::get('follow-ups', [FollowUpController::class, 'index']);

    Route::controller(TreatmentController::class)->prefix('treatments')->group(function () {
        Route::get('', 'index');
    });


    Route::controller(AppointmentController::class)->prefix('appointments')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('/{appointment}', 'update');
    });

    Route::controller(PhotoController::class)->prefix('photos')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::get('/{photo}', 'show');
        Route::post('/{photo}', 'update');
        Route::delete('/{photo}', 'destroy');
    });

    Route::controller(CampainController::class)->prefix('campains')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('/{campain}', 'update');
        Route::delete('/{campain}', 'destroy');
    });

    Route::controller(SMSTemplateController::class)->prefix('sms-templates')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('/{smsTemplate}', 'update');
        Route::delete('/{smsTemplate}', 'destroy');
    });

    Route::controller(SurvayController::class)->prefix('survays')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('/{survay}', 'update');
        Route::delete('/{survay}', 'destroy');

        Route::controller(SurvayQuestionController::class)->prefix('{survay}/questions')->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');
            Route::post('/reorder', 'reorder');
        });
    });

    Route::controller(SurvayQuestionController::class)->prefix('questions')->group(function () {
        Route::patch('/{question}', 'update');
        Route::delete('/{question}', 'destroy');
    });

    Route::controller(TreatmentPlanController::class)->prefix('treatment-plans')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::get('/{treatmentPlan}', 'show');
        Route::patch('/{treatmentPlan}', 'update');
        Route::delete('/{treatmentPlan}', 'destroy');
    });


    Route::controller(TreatmentController::class)->prefix('treatments')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::post('/reorder', 'reorder');
        Route::patch('/{treatment}', 'update');
        Route::delete('/{treatment}', 'destroy');

        Route::controller(TreatmentServiceController::class)->prefix('{treatment}/services')->group(function () {
            Route::get('', 'index');
            Route::post('', 'store');

            Route::middleware('forget.parameters:treatment')->group(function () {
                Route::patch('/{service}', 'update');
                Route::delete('/{service}', 'destroy');

                Route::controller(TreatmentServiceOptionController::class)->prefix('{service}/options')->group(function () {
                    Route::get('', 'index');
                    Route::post('', 'store');
    
                    Route::middleware('forget.parameters:service')->group(function () {
                        Route::patch('/{option}', 'update');
                        Route::delete('/{option}', 'destroy');
                    });
                });
            });
        });
    });
});
