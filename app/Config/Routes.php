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
$routes->post('posts/delete/(:num)', 'Posts::delete/$1');

$routes->post('posts/comment', 'Posts::addComment');
$routes->post('posts/comment/vote', 'Posts::voteComment');
$routes->post('posts/comment/add', 'Posts::addComment');

$routes->get('dashboard', 'Dashboard::index');
$routes->get('dashboard/getChartData', 'Dashboard::getChartData');
$routes->post('dashboard/deleteUser', 'Dashboard::deleteUser');
$routes->post('dashboard/deletePost', 'Dashboard::deletePost');
$routes->post('dashboard/deleteComment', 'Dashboard::deleteComment');
$routes->post('dashboard/deleteCommunity', 'Dashboard::deleteCommunity');


$routes->get('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');

$routes->post('auth/login', 'Auth::attemptLogin');
$routes->post('auth/register', 'Auth::attemptRegister');
$routes->get('auth/verify/(:segment)', 'Auth::verifyEmail/$1');
$routes->get('email/confirm', function() {
    return view('email/confirm');
});
$routes->get('email/confirmed', function() {
    return view('email/confirmed');
});

$routes->get('test-email', 'MailTest::sendTestEmail');

$routes->get('/settings', 'Settings::index');
$routes->post('/settings/updateAccount', 'Settings::updateAccount');
$routes->post('/settings/updateProfile', 'Settings::updateProfile');
$routes->post('/settings/deleteAccount', 'Settings::deleteAccount');

// Community Routes (Inside views/community/)
$routes->get('communities', 'Communities::index');
$routes->get('communities/view/(:num)', 'Communities::view/$1');
$routes->get('communities/create', 'Communities::create');
$routes->post('communities/store', 'Communities::store');
