<?php
session_start();

require "../vendor/autoload.php";

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new AltoRouter();

//$method, $route, $target, $name
// AUTH
$router->map('GET', '/login', 'App\Controllers\AuthController#loginForm', 'LoginForm');
$router->map('POST', '/login', 'App\Controllers\AuthController#login', 'Login');
$router->map('GET', '/signup', 'App\Controllers\AuthController#registerForm', 'RegisterUserForm');
$router->map('POST', '/signup', 'App\Controllers\AuthController#register', 'RegisterUser'); 
$router->map('GET', '/logout', 'App\Controllers\AuthController#logout', 'Logout');
//FRONT
$router->map('GET', '/', 'App\Controllers\PostController#index', 'HomePage'); 
$router->map('GET', '/blog', 'App\Controllers\PostController#displayPosts', 'AllPosts'); 
$router->map('GET', '/post/[i:id]', 'App\Controllers\PostController#displayPost', 'SinglePost'); 
$router->map('POST', '/comment/create', 'App\Controllers\CommentController#create', 'CreateComment'); 
$router->map('GET', '/commentdelete/[i:id]', 'App\Controllers\CommentController#delete', 'DeleteComment'); 
$router->map('POST', '/sendmail', 'App\Controllers\ContactController#handleForm', 'SendMail'); 
//BACK
//admin
$router->map('GET', '/admin', 'App\Controllers\AdminController#index', 'Dashboard'); 
//posts
$router->map('GET', '/admin/posts', 'App\Controllers\PostController#displayAdminPosts', 'AdminPosts'); 
$router->map('GET', '/admin/postsvalidated', 'App\Controllers\PostController#displayAdminValidatedPosts', 'AdminValidatedPosts'); 
$router->map('GET', '/admin/postsdrafted', 'App\Controllers\PostController#displayAdminDraftedPosts', 'AdminDraftedPosts'); 
$router->map('GET', '/admin/postcreate', 'App\Controllers\PostController#createForm', 'CreatePostForm'); 
$router->map('POST', '/admin/postcreate', 'App\Controllers\PostController#create', 'CreatePost'); 
$router->map('GET', '/admin/postupdate/[i:id]', 'App\Controllers\PostController#updateForm', 'UpdatePostForm'); 
$router->map('POST', '/admin/postupdate/[i:id]', 'App\Controllers\PostController#update', 'UpdatePost'); 
$router->map('GET', '/admin/postdelete/[i:id]', 'App\Controllers\PostController#delete', 'DeletePost'); 
//comments
$router->map('GET', '/admin/comments', 'App\Controllers\CommentController#displayAdminComments', 'AdminComments'); 
$router->map('GET', '/admin/commentsvalidated', 'App\Controllers\CommentController#displayAdminValidatedComments', 'AdminValidatedComments'); 
$router->map('GET', '/admin/commentsdrafted', 'App\Controllers\CommentController#displayAdminDraftedComments', 'AdminDraftedComments'); 
$router->map('GET', '/admin/commentdelete/[i:id]', 'App\Controllers\CommentController#adminDelete', 'AdminDeleteComment'); 
//users
$router->map('GET', '/admin/users', 'App\Controllers\UserController#displayAdminUsers', 'AdminUsers'); 
$router->map('GET', '/admin/usercreate', 'App\Controllers\UserController#createForm', 'AdminCreateUserForm'); 
$router->map('POST', '/admin/usercreate', 'App\Controllers\UserController#create', 'AdminCreateUser'); 
$router->map('GET', '/admin/userupdate/[i:id]', 'App\Controllers\UserController#updateForm', 'AdminUpdateUserForm'); 
$router->map('POST', '/admin/userupdate/[i:id]', 'App\Controllers\UserController#update', 'AdminUpdateUser'); 
$router->map('GET', '/admin/userdelete/[i:id]', 'App\Controllers\UserController#delete', 'AdminDeleteUser'); 
//categories
$router->map('GET', '/admin/categories', 'App\Controllers\CategoryController#displayAdminCategories', 'AdminCategories'); 
$router->map('GET', '/admin/categorycreate', 'App\Controllers\CategoryController#createForm', 'CreateCategoryForm'); 
$router->map('POST', '/admin/categorycreate', 'App\Controllers\CategoryController#create', 'CreateCategory'); 
$router->map('GET', '/admin/categoryupdate/[i:id]', 'App\Controllers\CategoryController#updateForm', 'UpdateCategoryForm'); 
$router->map('POST', '/admin/categoryupdate/[i:id]', 'App\Controllers\CategoryController#update', 'UpdateCategory'); 
$router->map('GET', '/admin/categorydelete/[i:id]', 'App\Controllers\CategoryController#delete', 'DeleteCategory'); 
//tags
$router->map('GET', '/admin/tags', 'App\Controllers\TagController#displayAdminTags', 'AdminTags'); 
$router->map('GET', '/admin/tagcreate', 'App\Controllers\TagController#createForm', 'CreateTagForm'); 
$router->map('POST', '/admin/tagcreate', 'App\Controllers\TagController#create', 'CreateTag'); 
$router->map('GET', '/admin/tagupdate/[i:id]', 'App\Controllers\TagController#updateForm', 'UpdateTagForm'); 
$router->map('POST', '/admin/tagupdate/[i:id]', 'App\Controllers\TagController#update', 'UpdateTag'); 
$router->map('GET', '/admin/tagdelete/[i:id]', 'App\Controllers\TagController#delete', 'DeleteTag'); 

$match = $router->match();

if ($match) {
    $target = $match["target"];

    if (is_string($target) && strpos($target, "#") !== false) {
        list($controller, $action) = explode("#", $target);

        if ($_GET) {
            $match['params']['get'] = $_GET;
        }
        if ($_POST) {
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
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo '404 not found baby';
    die();
}
