<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources(['user' => 'API\UserController']);

Route::get('profile', 'API\UserController@profile');

Route::put('profile', 'API\UserController@updateProfile');


Route::get('findUser', 'API\UserController@search');