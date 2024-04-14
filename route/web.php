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
use App\Controller\App;
use System\Route\Route;
use App\Controller\Profile;
use App\Controller\User;

$route = new Route();

$route->group('admin',[
    ['get','register',Auth::class."::register"]
]);

$route->group('password',[
    ['get','/forgot', Profile::class."::forgot"],
    ['get','/retrieve', Profile::class."::retrieve",['id','pass']]
]);