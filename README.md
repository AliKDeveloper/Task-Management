## Laravel Tasks Management

This is laravel Tasks Management project where we can manage tasks and subtasks and also there are different roles for different users in addition we can perform some custom artisan commands to specify jobs 

## Packages that are used in this project:
- [league/csv](https://csv.thephpleague.com/)
  we have used this package to take care of slug generation and make sure that the slug is unique.
- [sanctum](https://laravel.com/docs/11.x/sanctum) package for authentication
> Form more information about those packages you can read more in the official documentation

## List of API Endpoints

1. **(get) /api/users**: List all the users
2. **(post) /api/users**: store new users
3. **(put) /api/users/{user}**: Update the user
4. **(delete) /api/users/{user}**: delete the user
5. **(get) /api/tasks**: List all the tasks
6. **(post) /api/tasks**: store new tasks
7. **(get) /api/tasks/{task}**: Get specific task details
8. **(put) /api/tasks/{task}**: Update the task
9. **(delete) /api/tasks/{task}**: delete the task
10. **(post) /api/tasks-import**: Import tasks. 
11. **(get) /api/tasks-export**: Export all the tasks
12. **(get) /api/tasks-progress**: Get task progress
13. **(get) /import-progress/{batchId}**: Get import progress
14. **/api/login**: Login the user
15. **/api/register**: Register the user
> Note: We have used **auth:sanctum** middleware for authentication. So, we have to pass the token in the header of request.

## List of Artisan Commands
1. `php artisan user:create`: Create a new user
2. `php artisan tasks:export {filename=tasks_export.csv}`: Export tasks to a CSV file
3. `php artisan tasks:import {file}`: Import tasks from a CSV file 


## How to run the project

First of all you have to make sure that your computer have **composer** and local server such as **XAMPP**
Then follow these steps:
1. Download the project as zip file or clone it
    1. `git clone https://github.com/AliKDeveloper/Task-Management.git`
2. Open the project from IDE such as VS Code or PhpStorme and run the following commands inside the terminal:
    1. `composer install`
    2. `cp .env.example .env`
    3. `php artisan key:generate`
    4. `php artisan migrate`
3. run the project `php artisan serve`
4. Finally, use an application like **Postman** or **Insomnia** to test the project.
