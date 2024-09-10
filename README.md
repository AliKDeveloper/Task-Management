## Laravel Tasks Management API

This is laravel Tasks Management project where we can manage tasks and subtasks and also there are different roles for different users in addition we can perform some custom artisan commands to do specify jobs,

The program includes three user roles: Product owner, developer, and tester. Each role has distinct
responsibilities and can perform specific tasks.

> Note: as Project goes bigger we can add more roles in this project but for now we have only three roles (product owner, developer and tester).

### 1- Product Owner

The Product Owner manages tasks by creating, updating, and deleting them. They assign tasks to
developers and review completed tasks, marking them as DONE or returning them to IN PROGRESS.
They also manage users by creating, updating, and deleting profiles and assigning titles.


### 2- Developer

They are responsible to move only their tasks from TODO to IN PROGRESS and from IN PROGRESS to
READY FOR TEST.

### 3- Tester

They are responsible to move only their tasks from READY FOR TEST to PO REVIEW.

## Packages that are used in this project:

- [league/csv](https://csv.thephpleague.com/)
  we have used this package to deal with CSV files.
- [sanctum](https://laravel.com/docs/11.x/sanctum) package for authentication
> For more information about those packages you can read more in the official documentation

## List of API Endpoints

### Users:

1. **(get) /api/users**: List all the users
2. **(post) /api/users**: store new users
3. **(put) /api/users/{user}**: Update the user
4. **(delete) /api/users/{user}**: delete the user

### Tasks:

1. **(get) /api/tasks**: List all the tasks
2. **(post) /api/tasks**: store new tasks
3. **(get) /api/tasks/{task}**: Get specific task details
4. **(put) /api/tasks/{task}**: Update the task
5. **(delete) /api/tasks/{task}**: delete the task
6. **(post) /api/tasks-import**: Import tasks.
7. **(get) /api/tasks-export**: Export all the tasks
8. **(get) /api/tasks-progress**: Get task progress

### JobProgress:

1. **(get) /import-progress/{batchId}**: Get import progress

Change Task Status:

1. **(put) /api/tasks/{taskId}/status/{status}**: Change task status

### Subtasks:

1. **(get) /api/tasks/{taskId}/subtasks**: Get all subtasks for a specific task
2. **(post) /api/tasks/{taskId}/subtasks/{subtaskId}**: Get a specific subtask
3. **(post) /api/tasks/{taskId}/subtasks**: Create a new subtask
4. **(put) /api/tasks/{taskId}/subtasks/{subtaskId}**: Update a subtask
5. **(delete) /api/tasks/{taskId}/subtasks/{subtaskId}**: Delete a subtask

### Authentication:

1. **/api/login**: Login the user
2. **/api/register**: Register the user

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
