<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('notes', \App\Http\Controllers\Api\NoteController::class);
