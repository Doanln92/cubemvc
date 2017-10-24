<?php
/**
 * @author Doanln
 * @copyright 2017
 * @package Cube.http
 * 
 * set cac duong dan o day
 */


// home controller

Cube::set('/','GET', 'HomeController@index');
Cube::set('/test','GET', 'TestController@index');
Cube::set('/search','GET,POST', 'HomeController@search','search');
Cube::set('/live-search','GET,POST', 'HomeController@liveSearch','live-search');
Cube::set('/imgresize/*','GET,POST', 'ImageController@resize','resize');

Cube::set('/confirm','GET,POST', 'ConFirmController@confirm', 'confirm');

Cube::set('/{cat}.chn','GET', 'HomeController@viewCategory', 'cat_url1');
Cube::set('/{cat}/{child}.chn','GET', 'HomeController@viewDoubleCategory', 'cat_url2');
Cube::set('/chi-tiet/{postname}.html','GET', 'HomeController@viewPost', 'post');
Cube::set('/hot','GET', 'HomeController@hotPosts', 'hot');
Cube::set('/popular','GET', 'HomeController@popularPosts', 'popular');

Cube::set('/shop','GET,POST', 'HomeController@shopping', 'shopping');
Cube::set('/shop/cart','GET,POST', 'HomeController@shoppingCart', 'shopping_cart');
Cube::set('/shop/{act}','GET,POST', 'HomeController@shopping', 'shopping_act');
Cube::set('/shop/{act}/{item}','GET,POST', 'HomeController@shopping', 'shopping_item');

Cube::set('/cart','GET,POST', 'HomeController@cart', 'cart');
Cube::set('/cart/{act}','GET,POST', 'HomeController@cart', 'cart-act');
Cube::set('/cart/{act}/{item}','GET,POST', 'HomeController@cart', 'cart-item');
Cube::set('/cart/{act}/{item}/{val}','GET,POST', 'HomeController@cart', 'cart-item-val');

Cube::set('/subcribe','GET,POST', 'HomeController@subcribe', 'subcribe');


// dashboard

Cube::set('/dashboard','GET', 'AdminController@dashboard','dashboard');
Cube::set('/dashboard/upm','GET', 'AdminController@updateAllPostKeywords','updateAllPostKeywords');

Cube::set('/dashboard/{obj}','GET', 'AdminController@dashboard', 'dashboard_item');
Cube::set('/dashboard/{obj}/{act}','GET,POST', 'AdminController@dashboard','dashboard_action');
Cube::set('/dashboard/{obj}/{act}/{item}','GET,POST', 'AdminController@dashboard','dashboard_action_item');

Cube::set('/config','GET', 'WebConfigController@viewAll');
Cube::set('/config/add/{name}/{key}/{value}','GET', 'WebConfigController@AddConfig');



Cube::set('/login','GET,POST', 'userController@login','login');
Cube::set('/logout','GET', 'userController@logout','logout');
Cube::set('/register','GET,POST', 'UserController@register', 'register');
Cube::set('/forgot','GET,POST', 'UserController@forgot', 'forgot');
Cube::set('/reset-password','GET,POST', 'UserController@resetPassword', 'reset-password');



Cube::set('/profile','GET,POST', 'UserController@profile', 'profile');
Cube::set('/profile/{user}','GET,POST', 'UserController@profile', 'profile-info');
Cube::set('/profile/{user}/info','GET,POST', 'UserController@profile', 'prf');
Cube::set('/profile/{user}/{action}','GET,POST', 'UserController@profile', 'profile-action');




?>