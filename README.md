# FiremonPHP
Este projeto é uma iniciativa para tornar operações com o mongodb mais fáceis, visando legibilidade, desempenho e escalabilidade.

Firemon deverá se capaz de adicionar, atualizar, excluir e ler dados ou documentos do mongo, esse projeto não é nenhum ODM ou Mapper Data,
porém deve ser de interesse posterior, o Firemon nasce para trazer a mesma facilidade do Firebase porém com mongodb (local ou cluster cloud)


### Instanciando uma conexão
```
<?php
\FiremonPHP\Database\Connection\ConnectionManager::config('default', [
   'url' => 'mongodb://user:pass@localhost:27017',
    'database' => 'testdb'
]);
```

### Database

```
<?php
$database = new \FiremonPHP\Database\Database();
```
##### Database functions

```
get(string $urlNamespace)
```
Realiza uma consulta no banco de dados pelo filtro dados

```
set(array $data)
```

Constrói a partir do bloco de dados seus respectivos documentos, conforme exemplos a baixo:

### Adicionando dados

```
<php

$newData = $database->set([
    'users' => ['nome' => 'Marcos Dantas', 'cidade' => 'Parelhas'], // isso já é capaz de adicionar dados a partir de um namespace
    'users/Parelhas' => ['cidade' => 'Atualizou!'], // Isso já é capaz de atualizar todas ou uma única chave definida abaixo,
    'users/8748494984' => null, // aqui uma delecao usando a chave _id do mongo principal.
    'posts' => [ // Aqui vemos a possibilidade de numa mesma escrita adicionarmos um ou muitos documentos a outras coleções.
        ['title' => 'some title', 'description' => 'Alguns dados adicionais'],
        ['title' => 'Post de teste', 'description' => 'Alguns dados adicionais']
    ],
    'posts/984984' => null // Deletando o post de id 984984
]);

$newData
    ->replace('users') // Os dados serão sobrepostos
    ->many('posts') // Muitos documentos podem ser alterados
    ->setIndex('users', 'cidade'); // Setando um index para o filtro passado na chave da atualização ou deleção

$newData
    ->execute(); // Executa o conjunto de instruções!
```

### Lendo dados do mongodb

```
<?php

$users = $database->get('users')
    ->execute();

$posts = $database->get('posts')
    ->fields(['title','description'])
    ->descBy('created')
    ->limit(10)
    ->endAt('created', 'Implement data type fault!')
    ->startAt('created', 'Implement data type fault!')
    ->skip(5);
// ->notEqual()
// ->equalTo()
```
