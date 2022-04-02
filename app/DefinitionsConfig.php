<?php

use Aura\SqlQuery\QueryFactory;
use Delight\Auth\Auth;
use League\Plates\Engine;

return [
    PDO::class => function() {
        $db_host =  $_ENV['DB_HOST'];
        $db_name =  $_ENV['DB_NAME'];
        $db_user =  $_ENV['DB_USER'];
        $db_password =  $_ENV['DB_PASSWORD'];
        return new PDO("mysql:host=$db_host;dbname=$db_name","$db_user","$db_password");
    },
    Engine::class => function() {
        return new Engine('../app/views/');
    },
    Auth::class => function($container) {
        return new Auth($container->get('PDO'));
    },
    QueryFactory::class => function() {
        return new QueryFactory('mysql');
    },

];

