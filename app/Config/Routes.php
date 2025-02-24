<?php

use App\Controllers\Posts;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('profile/(:any)', 'Profile::index/$1');


$routes->get('/', 'Posts::landing');
$routes->get('posts', 'Posts::index');
$routes->get('posts/create', 'Posts::create');
$routes->post('posts/store', 'Posts::store');
$routes->post('posts/vote', 'Posts::vote');
$routes->get('posts/(:num)', 'Posts::view/$1');

$routes->post('posts/comment', 'Posts::addComment');
$routes->post('posts/comment/vote', 'Posts::voteComment');
$routes->post('posts/comment/add', 'Posts::addComment');

$routes->get('dashboard', 'Dashboard::index');
$routes->get('dashboard/getChartData', 'Dashboard::getChartData');

$routes->get('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');
$routes->post('auth/login', 'Auth::attemptLogin');
$routes->post('auth/register', 'Auth::attemptRegister');

