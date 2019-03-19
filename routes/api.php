<?php

use Illuminate\Http\Request;

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


Route::prefix('v1')->namespace('API')->group(function () {
    // Login
    Route::post('/login','AuthController@postLogin');
    // Register
    Route::post('/register','AuthController@postRegister');
    
    // Protected with APIToken Middleware
    Route::middleware('APIToken')->group(function () {
      // need login to use

      //list user rotes
      Route::get('/viewlist', 'UserListController@viewList');
      Route::post('/userconfirmtolist', 'UserListController@userConfirmToList');
      Route::post('/userenteronlist', 'UserListController@userEnterOnList');
      Route::post('/userquitlist', 'UserListController@userQuitList');
      Route::post('/userrecuselist', 'UserListController@userRecuseList');
  
      
      //list owner rotes
      
      Route::post('/createlist', 'OwnerListController@createList');
      Route::post('/editlist', 'OwnerListController@editList');
      Route::delete('/deletelist', 'OwnerListController@deleteList');
      Route::post('/invitemembertolist', 'OwnerListController@inviteMemberToList');
      Route::post('/ownerconfirmtolist', 'OwnerListController@ownerConfirmToList');
      Route::post('/ownerremovemember', 'OwnerListController@ownerRemoveMember');
      Route::post('/selectconductor', 'OwnerListController@selectConductor');
      
      //user rotes
      Route::post('/islogin', 'AuthController@isLogin');
      Route::get('/viewperfil', 'UserController@viewEditPerfilUser');
      Route::post('/editperfil', 'UserController@postEditPerfilUser');
      Route::post('/logout','AuthController@postLogout');

      //user Image rotes
      Route::post('/saveimage', 'UserController@saveImage');
      Route::post('/viewimageperfil', 'UserController@viewImagePerfil');
      Route::delete('/removeimageperfil', 'UserController@removeImagePerfil');
    });
  });

