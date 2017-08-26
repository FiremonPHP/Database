<?php

require __DIR__.'/vendor/autoload.php';

\FiremonPHP\Database\Connection\ConnectionManager::config('default', [
   'url' => 'mongodb://kasoneri:y84h65t16@localhost:27017',
    'database' => 'nfce'
]);

$database = \FiremonPHP\Database\Connection\ConnectionManager::get('default');

$arr = [
    'posts/59a1d34c3274c23b640033a2' => [
        'tags' => ['nosql', 'sql']
    ]
];

$database->set($arr)
    ->execute();



