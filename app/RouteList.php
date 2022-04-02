<?php
return [
['GET', '/login_page', ['App\Controllers\AuthController', 'login_page']],
['POST', '/login', ['App\Controllers\AuthController', 'login']],
['GET', '/logout', ['App\Controllers\UserController', 'logout']],
['GET', '/register_page', ['App\Controllers\AuthController', 'register_page']],
['POST', '/register', ['App\Controllers\AuthController', 'register']],
['GET', '/verify_email', ['App\Controllers\AuthController', 'verification']],

['GET', '/', ['App\Controllers\UserController', 'index']],
['GET', '/user_profile/{id:\d+}', ['App\Controllers\UserController', 'show']],

['GET', '/user_create', ['App\Controllers\UserController', 'create']],
['POST', '/user_store', ['App\Controllers\UserController', 'store']],

['GET', '/user_edit/{id:\d+}', ['App\Controllers\UserController', 'edit']],
['POST', '/user_update/{id:\d+}', ['App\Controllers\UserController', 'update']],

['GET', '/user_edit_security/{id:\d+}', ['App\Controllers\UserController', 'edit_security']],
['POST', '/user_update_security/{id:\d+}', ['App\Controllers\UserController', 'update_security']],

['GET', '/user_edit_status/{id:\d+}', ['App\Controllers\UserController', 'edit_status']],
['POST', '/user_update_status/{id:\d+}', ['App\Controllers\UserController', 'update_status']],

['GET', '/user_edit_avatar/{id:\d+}', ['App\Controllers\UserController', 'edit_avatar']],
['POST', '/user_update_avatar/{id:\d+}', ['App\Controllers\UserController', 'update_avatar']],

['GET', '/user_delete/{id:\d+}', ['App\Controllers\UserController', 'user_delete']],
];