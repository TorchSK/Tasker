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

Route::get('orders/countbydays/{daysago}', 'OrderController@countByDays');
Route::get('users/countbydays/{daysago}', 'UserController@countByDays');

Route::get('products/search', 'ProductController@inputSearch');

Route::get('products/filter', 'ProductController@filter');

Route::get('products/all', 'ProductController@all');
Route::get('product/{id}/sizes', 'ProductController@getSizes');

Route::get('categories/search', 'CategoryController@inputSearch');

Route::put('product/{id}', 'ProductController@apiUpdate');
Route::put('category/{id}', 'CategoryController@apiUpdate');

Route::post('product/{id}/xmlupdate', 'ProductController@xmlUpdate');


Route::put('seo/tool/{id}', 'SeoController@toolApiUpdate');

Route::put('page/{id}', 'PageController@apiUpdate');

Route::get('products/simplelist', 'ProductController@simpleList');


Route::get('view/pricelevel', 'ProductController@viewPriceLevel');

Route::get('search/{query}', 'UtilController@searchAll');

Route::post('/bulk/', 'ProductController@postBulk')->name('admin.postBulk');
