<?php


$database = new FiremonPHP\Database\Database();

$newPosts = $database->set([
   'users' => [
       ['nome' => 'Marcos Dantas', 'cidade' => 'Parelhas'] // Inserção de muitos registros
       //...
   ],
   'users/65165161' => [ // Aqui é uma atualização
       'nome' => 'Marcos Dantas',
       'cidade' => 'Parelhas'
   ],
   'users/84949' => null // Deleção aqui
]);