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

//Route::get('/test', function () {
//
//});

//Auth::routes();
// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
$this->post('register', 'Auth\RegisterController@register');

Route::view('/', 'index')->name('index');

/* Posts */
Route::get('/blogs/{blog}/posts/{post}', 'PostController@show')->name('blogs.posts.show');
Route::get('/blogs/{blog}/posts', 'PostController@index')->name('blogs.posts.index');

/* Blogs */
Route::resource('blogs', 'BlogController', [
    'only' => ['create', 'destroy', 'index', 'store']
]);