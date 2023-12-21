<?php

require "../vendor/autoload.php";

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new AltoRouter();

//$method, $route, $target, $name
//FRONT
$router->map('GET', '/', 'App\Controllers\PostController#index', 'HomePage');//✔️
$router->map('GET', '/blog', 'App\Controllers\PostController#displayPosts', 'AllPosts');//✔️
$router->map('GET', '/post/[i:id]', 'App\Controllers\PostController#displayPost', 'SinglePost');//✔️
$router->map('POST', '/comment/create', 'App\Controllers\CommentController#create', 'CreateComment');//✔️
$router->map('GET', '/contact', function(){echo('CONTACT');}, 'contact');
$router->map('GET', '/a-propos', function(){echo('A PROPOS');}, 'APropos');
//BACK
    //admin
$router->map('GET', '/admin', 'App\Controllers\AdminController#index', 'Dashboard');//✔️ afficher dashboard
$router->map('GET', '/admin/documents', 'App\Controllers\AdminController#docForm', 'AddDocForm');
$router->map('GET', '/admin/mails', 'App\Controllers\AdminController#displayMails', 'AllMails');
    //posts
$router->map('GET', '/admin/posts', 'App\Controllers\PostController#displayAdminPosts', 'AdminPosts');//✔️
$router->map('GET', '/admin/postcreate', 'App\Controllers\PostController#createForm', 'CreatePostForm');//✔️
$router->map('POST', '/admin/postcreate', 'App\Controllers\PostController#create', 'CreatePost');//✔️
$router->map('GET', '/postupdate/[i:id]', 'App\Controllers\PostController#updateForm', 'UpdatePostForm');//🔴
$router->map('POST', '/postupdate/[i:id]', 'App\Controllers\PostController#update', 'UpdatePost');//🔴
$router->map('GET', '/admin/postdelete/[i:id]', 'App\Controllers\PostController#delete', 'DeletePost');//✔️
    //comments
$router->map('GET', '/admin/comments', 'App\Controllers\CommentController#displayAdminComments', 'AdminComments');//✔️
$router->map('GET', '/admin/commentdelete/[i:id]', 'App\Controllers\CommentController#delete', 'DeleteComment');//✔️
    //users
$router->map('GET', '/admin/users', 'App\Controllers\UserController#displayAdminUsers', 'AdminUsers');//✔️
$router->map('GET', '/admin/usercreate', 'App\Controllers\UserController#createForm', 'CreateUserForm');//✔️
$router->map('POST', '/admin/usercreate', 'App\Controllers\UserController#create', 'CreateUser');//✔️
$router->map('GET', '/admin/userdelete/[i:id]', 'App\Controllers\UserController#delete', 'DeleteUser');//✔️
$router->map('GET', '/admin/profil', 'App\Controllers\UserController#displayAdminProfil', 'AdminProfil');
    //categories
$router->map('GET', '/admin/categories', 'App\Controllers\CategoryController#displayAdminCategories', 'AdminCategories');//✔️
$router->map('GET', '/admin/categorycreate', 'App\Controllers\CategoryController#createForm', 'CreateCategoryForm');//✔️
$router->map('POST', '/admin/categorycreate', 'App\Controllers\CategoryController#create', 'CreateCategory');//✔️
$router->map('GET', '/categoryupdate/[i:id]', 'App\Controllers\CategoryController#updateCategorieForm', 'UpdateCategorieForm');//🔴
$router->map('POST', '/categoryupdate/[i:id]', 'App\Controllers\CategoryController#updateCategorie', 'UpdateCategorie');//🔴
$router->map('GET', '/admin/categorydelete/[i:id]', 'App\Controllers\CategoryController#delete', 'DeleteCategorie');//✔️
    //tags
$router->map('GET', '/admin/tags', 'App\Controllers\TagController#displayAdminTags', 'AdminTags');//✔️
$router->map('GET', '/admin/tagcreate', 'App\Controllers\TagController#createForm', 'CreateTagForm');//✔️
$router->map('POST', '/admin/tagcreate', 'App\Controllers\TagController#create', 'CreateTag');//✔️
$router->map('GET', '/admin/tagupdate/[i:id]', 'App\Controllers\TagController#updateForm', 'UpdateTagForm');//✔️
$router->map('POST', '/admin/tagupdate/[i:id]', 'App\Controllers\TagController#update', 'UpdateTag');//✔️
$router->map('GET', '/admin/tagdelete/[i:id]', 'App\Controllers\TagController#delete', 'DeleteTag');//✔️
//OTHERS
$router->map('POST', '/test', 'App\Controllers\TestController#test', 'test');

$match = $router->match();

if ($match) {
    $target = $match["target"];

    if (is_string($target) && strpos($target, "#") !== false) {
        list($controller, $action) = explode("#", $target);

        if($_GET) {
            $match['params']['get'] = $_GET;
        }
        if($_POST) {
            $match['params']['post'] = array_merge($_POST, $_FILES);
        }

        $params = $match["params"];
        $controller = new $controller();
        $controller->$action($params);
    } else {
        if (is_callable($target)) {
            call_user_func_array($target, $match["params"]);
        } else {
            require $target;
        } 
    }
} else {
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo '404 not found baby';
    die();
}