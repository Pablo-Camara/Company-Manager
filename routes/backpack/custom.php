<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers',
], function () { // custom admin routes
    Route::crud('document', 'DocumentCrudController');
    Route::get('documents/download/{document}', 'DocumentCrudController@downloadDocument');
    Route::crud('physical-space', 'PhysicalSpaceCrudController');
    Route::crud('anomaly', 'AnomalyCrudController');
    Route::crud('configuration', 'ConfigurationCrudController');
    Route::crud('equipment', 'EquipmentCrudController');
}); // this should be the absolute last line of this file