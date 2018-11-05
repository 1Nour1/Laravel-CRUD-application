<?php

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


/*
Route::get('/hello', function () {
    return '<h1> bonjour </h1>';
});

//insert dynamic values into url and i can pass more than one
Route::get('/users/{id}/{name}',function($id,$name){
    return 'This is user '.$name.' whith id '.$id;
});
*/


//Note: you mustn't return view from route, i have to create a controlelr and 
//set the route to go to certain controller function and then return the view
Route::get('/', 'PagesController@index');
Route::get('/about','PagesController@about');
Route::get('/services','PagesController@services');

Route::resource('posts','PostsController');//Here this is instead of making routes for each single one in PostsController
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
