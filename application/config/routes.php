<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['posts/index'] = 'posts/index';
$route['posts/create'] = 'posts/create';
$route['posts/update'] = 'posts/update';
$route['posts/(:any)'] = 'posts/view/$1';
$route['users/profile/(:any)'] = 'users/profile/$1';
$route['users/upload/(:any)'] = 'users/upload/$1';
$route['users/fetch'] = 'users/fetch';
$route['users/avatar'] = 'users/avatar';
$route['messages/send_message'] = 'messages/send_message';



$route['posts'] = 'posts/index';

$route['default_controller'] = 'pages/view';

$route['countries'] = 'countries/index';
$route['countries/create/(:any)'] = 'countries/create/$1';
$route['countries/posts/(:any)'] = 'countries/posts/$1';

$route['(:any)'] = 'pages/view/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
