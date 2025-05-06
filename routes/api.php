<?php

use App\Models\Auditlogs;
use App\Models\Customers;
use App\Models\Sites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/audit-logs', function (Request $request) {
    return response()->json(Auditlogs::all());
})->middleware('auth:sanctum');

Route::get('/customers', function (Request $request) {
    return response()->json(Customers::all());
})->middleware('auth:sanctum');

Route::get('/sites', function (Request $request) {
    return response()->json(Sites::all());
})->middleware('auth:sanctum');
