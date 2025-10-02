<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthRequest;
use App\Http\Controllers\MainPageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::post('/login', [AuthRequest::class, 'login'])->name('login');


Route::get('/logout', [AuthRequest::class, 'logout'])->name('logout')->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/articles', function (Request $request) {

})
->name('search.api')->middleware('auth:sanctum');

    