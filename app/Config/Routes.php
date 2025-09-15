<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Routes d'authentification
$routes->post('register', 'AuthController::register');
$routes->post('login', 'AuthController::login');
$routes->post('logout', 'AuthController::logout');

// Routes CRUD voitures (protégées par JWT)
$routes->group('voitures', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'VoitureController::index');
    $routes->get('(:num)', 'VoitureController::show/$1');
    $routes->post('/', 'VoitureController::create');
    $routes->put('(:num)', 'VoitureController::update/$1');
    $routes->delete('(:num)', 'VoitureController::delete/$1');
});