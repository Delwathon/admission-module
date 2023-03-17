<?php

use Illuminate\Support\Facades\Route;
use Modules\Admission\Http\Controllers\EnrolController;
use Modules\Admission\Http\Controllers\MultipleEnrolController;

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

// Route::prefix('admission')->group(function() {
//     Route::get('/', 'AdmissionController@index');
// });

Route::resource('enroll', EnrolController::class);
        Route::resource('import-enroll', MultipleEnrolController::class);
        Route::get('/enroll_sheet', [MultipleEnrolController::class, 'download'])->name('enroll_sheet.download');