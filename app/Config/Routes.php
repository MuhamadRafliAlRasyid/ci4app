<?php

use App\Controllers\Komik;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(true);

$routes->get('/', 'pages::index');
$routes->get('/pages', 'pages::index');
$routes->get('/pages/about', 'pages::about');
$routes->get('/pages/contact', 'pages::contact');
$routes->get('/komik', 'komik::index');
$routes->get('/komik/create', 'komik::create');
$routes->post('/komik/save', 'komik::save');
$routes->get('/komik/(:segment)', 'Komik::detail/$1');
