<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//users


$route['api/users/get-users']['get'] = 'APIUsers/get_all_users';
$route['api/users/get-user/(:num)']['get'] = 'APIUsers/get_user/$1';
$route['api/users/add-user']['post'] = 'APIUsers/post_user/';
$route['api/users/update-user/(:num)']['patch'] = 'APIUsers/patch_user/$1';
$route['api/users/delete-user/(:num)']['delete'] = 'APIUsers/delete_user/$1';

//projects

$route['api/projects/get-projects']['get'] = 'APIProjects/get_all_projects';
$route['api/projects/get-project/(:num)']['get'] = 'APIProjects/get_project/$1';
$route['api/projects/(:num)/get-projects']['get'] = 'APIProjects/get_projects_by_user/$1';
$route['api/projects/add-project']['post'] = 'APIProjects/post_project/';
$route['api/projects/update-project/(:num)']['patch'] = 'APIProjects/patch_project/$1';
$route['api/projects/delete-project']['delete'] = 'APIProjects/delete_project/$1';


//tasks
$route['api/tasks/get-tasks']['get'] = 'APITasks/get_all_tasks';
$route['api/tasks/get-task/(:num)']['get'] = 'APITasks/get_task/$1';
$route['api/tasks/(:num)/get-tasks']['get'] = 'APITasks/get_tasks_by_project/$1';
$route['api/tasks/add-task']['post'] = 'APITasks/post_task/';
$route['api/tasks/update-task/(:num)']['patch'] = 'APITasks/patch_task/$1';
$route['api/tasks/delete-task']['delete'] = 'APITasks/delete_task/$1';
