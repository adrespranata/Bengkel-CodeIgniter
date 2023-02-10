<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

//Auth Routes
$routes->get('/', 'Home::index');
$routes->get('/auth/login', 'Login::index');

//Dashboard
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'Validasilogin']);
$routes->get('/auth/dashboard', 'Dashboard::index', ['filter' => 'Validasilogin']);

//Staf
$routes->get('/auth/staf', 'Staf::index', ['filter' => 'Validasilogin']);

//Transaksi
$routes->get('/auth/transaksi', 'Transaksi::index', ['filter' => 'Validasilogin']);

//Data dan Laporan
$routes->get('/auth/sparepart', 'Sparepart::index', ['filter' => 'Validasilogin']);

$routes->get('/auth/supplier', 'Supplier::index', ['filter' => 'Validasilogin']);

$routes->get('/auth/pelanggan', 'Pelanggan::index', ['filter' => 'Validasilogin']);

$routes->get('/auth/purchase', 'Purchase::index', ['filter' => 'Validasilogin']);

$routes->get('/auth/sale', 'Sale::index', ['filter' => 'Validasilogin']);

$routes->get('/auth/laporan', 'Laporan::index', ['filter' => 'Validasilogin']);
$routes->get('/auth/laporan/laporansale', 'Laporan::laporansale', ['filter' => 'Validasilogin']);
//Konfigurasi Web dan User
$routes->get('/auth/konfigurasi/web', 'Konfigurasi::index', ['filter' => 'Validasilogin']);
$routes->get('/auth/konfigurasi/user', 'Konfigurasi::user', ['filter' => 'Validasilogin']);


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
