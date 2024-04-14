<?php 
/**
 * ABS MVC Framework
 *
 * @created      2023
 * @version      1.0.1
 * @author       abdursoft <support@abdursoft.com>
 * @copyright    2024 abdursoft
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
*/

use App\Controller\Admin\Auth;
use App\Controller\Admin\Subadmin;
use System\Route\Route;
use App\Controller\App;
use App\Controller\Controls\Brand;
use App\Controller\Controls\Category;
use App\Controller\Controls\Products;
use App\Controller\Controls\Sub_category;
use App\Controller\Controls\Suppliers;

$route = new Route();

$route->get('', App::class."::index");

// admins login & register route 
$route->group('admins',[
    ['post','register',Auth::class."::register"],
    ['post','login',Auth::class."::login"],
]);

//sub admins
$route->get('/set-pass/sub-admin',Subadmin::class."::setPass");
$route->middleware('admin',[
    ['post','/sub-admin/create', Subadmin::class."::create"],
],);

$route->middleware('admin',[
    // brand start 
    ['get','/brand', Brand::class."::get",['token']],
    ['post','/brand/create', Brand::class."::create"],
    ['post','/brand/update', Brand::class."::update"],
    ['delete','/brand/delete', Brand::class."::delete",['token']],
    // brand end 

    // category start 
    ['get','/category', Category::class."::get",['token']],
    ['post','/category/create', Category::class."::create"],
    ['post','/category/update', Category::class."::update"],
    ['post','/category/delete', Category::class."::delete",['token']],
    // category end 

    // sub-category start 
    ['get','/sub-category', Sub_category::class."::get",['token']],
    ['post','/sub-category/create', Sub_category::class."::create"],
    ['post','/sub-category/update', Sub_category::class."::update"],
    ['post','/sub-category/delete', Sub_category::class."::delete",['token']],
    // sub-category end 

    // sub-category start 
    ['get','/suppliers', Suppliers::class."::get",['token']],
    ['post','/suppliers/create', Suppliers::class."::create"],
    ['post','/suppliers/update', Suppliers::class."::update"],
    ['post','/suppliers/delete', Suppliers::class."::delete",['token']],
    // sub-category end 

    // products start 
    ['get','/products', Products::class."::get",['token']],
    ['post','/products/create', Products::class."::create"],
    ['post','/products/update', Products::class."::update"],
    ['post','/products/delete', Products::class."::delete",['token']],
    // products end 

],'sub_admin');