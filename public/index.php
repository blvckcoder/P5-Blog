<?php

require "../vendor/autoload.php";

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new AltoRouter();

//$method, $route, $target, $name
//FRONT
$router->map('GET', '/', 'App\Controllers\PostController#index', 'HomePage');
$router->map('GET', '/blog', 'App\Controllers\PostController#displayPosts', 'AllPosts');
$router->map('GET', '/post/[i:id]', 'App\Controllers\PostController#displayPost', 'SinglePost');
$router->map('POST', '/comment/create', 'App\Controllers\CommentController#create', 'CreateComment');
$router->map('GET', '/contact', function(){echo('CONTACT');}, 'contact');
$router->map('GET', '/a-propos', function(){echo('A PROPOS');}, 'APropos');
//BACK
    //admin
$router->map('GET', '/admin', 'App\Controllers\AdminController#index', 'Dashboard');
$router->map('GET', '/admin/documents', 'App\Controllers\AdminController#docForm', 'AddDocForm');
$router->map('GET', '/admin/mails', 'App\Controllers\AdminController#displayMails', 'AllMails');
    //posts
$router->map('GET', '/admin/posts', 'App\Controllers\AdminController#displayAdminPosts', 'AdminPosts');
$router->map('GET', '/admin/postcreate', 'App\Controllers\AdminController#createPostForm', 'CreatePostForm');
$router->map('POST', '/post/create', 'App\Controllers\AdminController#createPost', 'CreatePost');
$router->map('GET', '/postupdate/[i:id]', 'App\Controllers\AdminController#updatePostForm', 'UpdatePostForm');
$router->map('POST', '/postupdate/[i:id]', 'App\Controllers\AdminController#updatePost', 'UpdatePost');
$router->map('GET', '/admin/postdelete/[i:id]', 'App\Controllers\AdminController#deletePost', 'DeletePost');
    //comments
$router->map('GET', '/admin/comments', 'App\Controllers\AdminController#displayAdminComments', 'AdminComments');
$router->map('GET', '/admin/commentdelete/[i:id]', 'App\Controllers\AdminController#deleteComment', 'DeleteComment');
    //users
$router->map('GET', '/admin/users', 'App\Controllers\AdminController#displayAdminUsers', 'AdminUsers');
$router->map('GET', '/admin/profil', 'App\Controllers\AdminController#displayAdminProfil', 'AdminProfil');
    //categories
$router->map('GET', '/admin/categories', 'App\Controllers\AdminController#displayAdminCategories', 'AdminCategories');
$router->map('GET', '/admin/categoriecreate', 'App\Controllers\AdminController#createCategorieForm', 'CreateCategorieForm');
$router->map('POST', '/categorie/create', 'App\Controllers\AdminController#createCategorie', 'CreateCategorie');
$router->map('GET', '/categorieupdate/[i:id]', 'App\Controllers\AdminController#updateCategorieForm', 'UpdateCategorieForm');
$router->map('POST', '/categorieupdate/[i:id]', 'App\Controllers\AdminController#updateCategorie', 'UpdateCategorie');
$router->map('GET', '/admin/categoriedelete/[i:id]', 'App\Controllers\AdminController#deleteCategorie', 'DeleteCategorie');
    //Tags
$router->map('GET', '/admin/tags', 'App\Controllers\AdminController#displayAdminTags', 'AdminTags');
$router->map('GET', '/admin/tagcreate', 'App\Controllers\AdminController#createTagForm', 'CreateTagForm');
$router->map('POST', '/tag/create', 'App\Controllers\AdminController#createTag', 'CreateTag');
$router->map('GET', '/tagupdate/[i:id]', 'App\Controllers\AdminController#updateTagForm', 'UpdateTagForm');
$router->map('POST', '/tagupdate/[i:id]', 'App\Controllers\AdminController#updateTag', 'UpdateTag');
$router->map('GET', '/admin/tagdelete/[i:id]', 'App\Controllers\AdminController#deleteTag', 'DeleteTag');
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