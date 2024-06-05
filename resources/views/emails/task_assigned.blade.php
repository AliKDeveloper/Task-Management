<!DOCTYPE html>
<html>
<head>
    <title>New Task Assigned</title>
</head>
<body>
    <h1>New Task Assigned</h1>
    <p>Dear {{ $task->assignedUser->name }},</p>
    <p>You have been assigned a new task: <strong>{{ $task->title }}</strong></p>
    <p>Description: {{ $task->description }}</p>
    <p>Due Date: {{ $task->due_date }}</p>
    <p>Status: {{ $task->status }}</p>
</body>
</html>
