# Criando o projeto em Laravel

## Iniciando o Projeto

```
composer create-project laravel/laravel {Nome do Projeto}
```

ou

```
laravel new {Nome do Projeto}
```

# Configurações

## Configurando o banco de dados

### Utilizando Sqlite

Vem por padrão no projeto de Laravel 11

### Utilizando Mysql

no arquivo `.env`

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your database name(laravel_11_jwt)
DB_USERNAME=your database username(root)
DB_PASSWORD=your database password(root)
```

## Criando Rota de Api

```
php artisan install:api
```

## Instalando e configurando JWT Auth Package

```
composer require php-open-source-saver/jwt-auth
```

### publish the package config file:

```
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
```

### Gerar chave secreta

```
php artisan jwt:secret
```

### Mudança no config/auth.php

```
'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],
```

# Modelo

## Modificações no Model User

adicionar

```
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
```

e os metodos

```
public function getJWTIdentifier()

    {
        return $this->getKey();
    }

public function getJWTCustomClaims()
    {
        return [];
    }
```

### OBS: Para popular o banco de Task

Criar o modelo de para Tarefas (Task)

```
php artisan make:model task -a
```

### Schema para o banco de Tasks

```
Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_complete')->default(false);
            $table->timestamps();
        });
```

# Controladores

## Criar os Controladores

```
php artisan make:controller Api/AuthController
```

implementar os metodos de login, register, profile, refresh-token, logout

```
php artisan make:controller Api/TaskController
```

implementar os metodos index e show

### Modificar rotas para os metodos

Implementar o middleware para autenticação

```
<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post("register", [ApiController::class, 'register']);
Route::post("login", [ApiController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::apiResource('tasks', TaskController::class);
    Route::get('profile', [ApiController::class, 'profile']);
    Route::get('refresh-token', [ApiController::class, 'refreshToken']);
    Route::get('logout', [ApiController::class, 'logout']);
});
```

# Rotas

### Rotas Não Autenticadas

/api/register -> POST [Necessário Realizar o Registro para Logar]

/api/login -> POST [Realiza Login de Usuário Registrado] | Retorna o Token de Acesso

# Rotas Autenticadas

/api/tasks -> GET [Retorna Todas as Tarefas] | Necessário autenticação com JWT

/api/tasks/{id} -> GET [Retorna a Tarefas especifica]

/api/profile -> GET [Retorna dados do Perfil]

/api/logout -> GET [ Realiza Logout]

/api/refresh-token -> GET [Atualiza o Token De acesso]

# Documentação feita no Swagger

```
/api/documentation
```

# Iniciar a Aplicação

```
php artisan serve
```
