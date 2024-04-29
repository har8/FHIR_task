<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CernerFhirController;
use App\Http\Controllers\SmartFhirController;

#Cerner Bulk data access
Route::controller(CernerFhirController::class)->prefix('/cerner-fhir')->group(function() {
    Route::get('/requestAccessToken', 'requestAccessToken');
    Route::get('/prepare', 'prepareExport');
    Route::get('/status', 'getBulkStatus');
    Route::get('/show', 'view');
    Route::get('/fetch/{key}', 'fetchData');    
});

#Smart Bulk data access
Route::controller(SmartFhirController::class)->prefix('/smart-fhir')->group(function() {
    Route::get('/prepare', 'prepareExport');
    Route::get('/status', 'getBulkStatus');
    Route::get('/show', 'view');
    Route::get('/fetch/{key}', 'fetchData');
});
