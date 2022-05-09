<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['default_controller'] = 'Main';
$route['portfolio'] = 'main/portfolio';
$route['suppliers'] = 'main/suppliers';
$route['dishes'] = 'main/dishes';

$route['main'] = 'Main';
$route['about'] = 'main/about';
$route['login'] = 'main/login';
$route['profile'] = 'main/account';
$route['nameproduct '] = 'main/nameproduct';
$route['registration'] = 'main/registration';
$route['logout'] = 'main/logout';
$route['list_orders'] = 'main/list_orders';
$route['add_order'] = 'main/add_order';

$route['edit_order(:num)'] = 'main/edit_order/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
