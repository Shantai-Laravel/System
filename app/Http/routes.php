<?php

Route::get('/auth/login', 'Auth\CustomAuthController@login');
Route::post('/auth/login', 'Auth\CustomAuthController@checkLogin');
Route::get('/auth/register', 'Auth\CustomAuthController@register');
Route::post('/auth/register', 'Auth\CustomAuthController@checkRegister');
Route::get('/auth/logout', 'Auth\CustomAuthController@logout');


Route::group(['prefix' => 'back', 'middleware' => 'auth'], function () {

    Route::get('/set-language/{lang}', 'LanguagesController@set')->name('set.language');

    Route::get('/', 'Admin\DefaultController@index');


    Route::resource('/pages', 'Admin\PagesController');
    Route::resource('/modules', 'Admin\ModulesController');
    Route::resource('/forms', 'Admin\FormsController');
    Route::resource('/categories', 'Admin\GoodsCategoriesController');

    Route::group(['prefix' => 'settings'], function () {

        Route::resource('/languages', 'Admin\LanguagesController');
        Route::patch('/languages/set-default/{id}', 'Admin\LanguagesController@default')->name('languages.default');

//    Route::patch('/languages/default/{id}', 'Admin\LanguagesController@default')->name('languages.default');

    });

});


Route::group([
    'prefix' => '{lang?}',
], function () {
    Route::group([
        'prefix' => 'back',
    ], function () {




        Route::get('/platform/pages', 'Admin\PlatformController@index');
        Route::get('/platform/update', 'Admin\PlatformController@update');
        Route::any('/upload', 'FileController@upload');
        Route::get('/', 'Admin\DefaultController@index');



//        Route::any('/{module}/{submenu?}/{action?}/{id?}/{lang_id?}', ['uses' => 'RoleManager@routeResponder']);

    });


    Route::group([], function () {

        // all pages
        Route::get('/', 'Front\PagesController@index');

        Route::group(['middleware' => 'auth_front'], function () {
            // for users
        });

        // temp Route
        // Route::get('/register', 'Front\UserController@register');
        // Route::post('/registerPost', 'Front\UserController@postRegister');

    });

});
