<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;


class TaskService
{

    const STATUS_MAP = [
        1 => 'To Do',
        2 => 'In Progress',
        3 => 'Done',
    ];

    public function getTasksForUser(User $user): Collection
    {
        return $user->tasks()
                    ->orderByDesc('created_at')
                    ->get();
    }

    public function createTask(User $user, array $data): Task
    {
        $task = new Task($data);
        $task->user_id = $user->id;
        $task->save();

        return $task;
    }


    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
    }
}