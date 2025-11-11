
TASK TRACKER API
RESTFul AAPI, simple for manage personal tasks, securized with Laravel Sanctum

Stack used :
    Laravel 
    Auth : Sanctum
    SQLite
    PHPUnit

Why Sanctum? Default included, adapted for APi first-party, API made and controled by the service provider.

Setup install :

2. Install dependancies
command :   composer install

3.. Config environment 
command :   cp .env.example .env
            php artisan key:generate

4. Database
command :   touch database/database.sqlite
            php artisan migrate
            
5. Run server 
command :   php artisan serve

6. Running tests PHPUnit
command :   php artisan test



Routes: 
    POST       api/auth/login           login with user
    POST       api/auth/register        create new user

Tasks routes need Bearer Auth Jeton avec sanctum
    GET        api/tasks                list user's connected all tasks 
    POST       api/tasks                add new task for connected user
    GET        api/tasks/{task}         get one precised task of connected user
    PATCH      api/tasks/{task}         update precised task of connected user
    DELETE     api/tasks/{task}         delete task for connected user