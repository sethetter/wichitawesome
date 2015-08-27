<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// \DB::listen(
//     function ($sql, $bindings, $time) {
//         echo '<pre>';
//         var_dump($sql);
//         echo '</pre>';
//     }
// );

Route::get('/', ['uses' => 'EventController@index', 'as' => 'events.index']);

// Authentication
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

Route::get('feedback', 'FeedbackController@index');
Route::post('feedback', 'FeedbackController@send');

// Registration
//Route::get('auth/register', 'Auth\AuthController@getRegister');
//Route::post('auth/register', 'Auth\AuthController@postRegister');

// @TODO: Enable this when the homepage is updated
//Route::get('events', ['uses' => 'EventController@index', 'as' => 'events.index']);
Route::get('events/submit', ['uses' => 'EventController@submit', 'as' => 'events.submit']);
Route::get('events/admin', ['uses' => 'EventController@admin', 'as' => 'events.admin', 'middleware' => 'auth']);
Route::get('events/create', ['uses' => 'EventController@create', 'as' => 'events.create', 'middleware' => 'auth']);
Route::get('events/{id}/export', ['uses' => 'EventController@export', 'as' => 'events.export']);
Route::get('events/{id}', ['uses' => 'EventController@show', 'as' => 'events.show']);
Route::get('events/{id}/edit', ['uses' => 'EventController@edit', 'as' => 'events.edit', 'middleware' => 'auth']);
Route::post('events/submit', ['uses' => 'EventController@collect', 'as' => 'events.collect']);
Route::post('events', ['uses' => 'EventController@store', 'as' => 'events.store']);
Route::put('events/{id}', ['uses' => 'EventController@update', 'as' => 'events.update', 'middleware' => 'auth']);
Route::delete('events/{id}', ['uses' => 'EventController@destroy', 'as' => 'events.destroy', 'middleware' => 'auth']);

Route::get('permissions/admin', ['uses' => 'PermissionController@admin', 'as' => 'permissions.admin', 'middleware' => 'auth']);
Route::resource('permissions', 'PermissionController', ['except' => ['index'], 'middleware' => 'auth']);

Route::get('roles/admin', ['uses' => 'RoleController@admin', 'as' => 'roles.admin', 'middleware' => 'auth']);
Route::resource('roles', 'RoleController', ['except' => ['index'], 'middleware' => 'auth']);

Route::get('venues', ['uses' => 'VenueController@index', 'as' => 'venues.index']);
Route::get('venues/submit', ['uses' => 'VenueController@submit', 'as' => 'venues.submit']);
Route::get('venues/admin', ['uses' => 'VenueController@admin', 'as' => 'venues.admin', 'middleware' => 'auth']);
Route::get('venues/create', ['uses' => 'VenueController@create', 'as' => 'venues.create', 'middleware' => 'auth']);
Route::get('venues/{id}', ['uses' => 'VenueController@show', 'as' => 'venues.show']);
Route::get('venues/{id}/edit', ['uses' => 'VenueController@edit', 'as' => 'venues.edit', 'middleware' => 'auth']);
Route::post('venues', ['uses' => 'VenueController@store', 'as' => 'venues.store']);
Route::put('venues/{id}', ['uses' => 'VenueController@update', 'as' => 'venues.update', 'middleware' => 'auth']);
Route::delete('venues/{id}', ['uses' => 'VenueController@destroy', 'as' => 'venues.destroy', 'middleware' => 'auth']);

Route::get('organizations', ['uses' => 'OrganizationController@index', 'as' => 'organizations.index']);
Route::get('organizations/submit', ['uses' => 'OrganizationController@submit', 'as' => 'organizations.submit']);
Route::get('organizations/admin', ['uses' => 'OrganizationController@admin', 'as' => 'organizations.admin', 'middleware' => 'auth']);
Route::get('organizations/create', ['uses' => 'OrganizationController@create', 'as' => 'organizations.create', 'middleware' => 'auth']);
Route::get('organizations/{id}', ['uses' => 'OrganizationController@show', 'as' => 'organizations.show']);
Route::get('organizations/{id}/edit', ['uses' => 'OrganizationController@edit', 'as' => 'organizations.edit', 'middleware' => 'auth']);
Route::post('organizations', ['uses' => 'OrganizationController@store', 'as' => 'organizations.store']);
Route::put('organizations/{id}', ['uses' => 'OrganizationController@update', 'as' => 'organizations.update', 'middleware' => 'auth']);
Route::delete('organizations/{id}', ['uses' => 'OrganizationController@destroy', 'as' => 'organizations.destroy', 'middleware' => 'auth']);

Route::get('users/admin', ['uses' => 'UserController@admin', 'as' => 'users.admin', 'middleware' => 'auth']);
Route::resource('users', 'UserController', ['except' => ['index'], 'middleware' => 'auth']);

Route::get('api/venues', 'VenueController@getVenues');
Route::get('api/venues/location', 'VenueController@getVenuesByLocation');
Route::get('api/venues/{id}', 'VenueController@getVenue');
Route::get('api/events', 'EventController@getEvents');
Route::get('api/view/events', 'EventController@viewEvents');
Route::get('api/events/{id}', 'EventController@getEvent');