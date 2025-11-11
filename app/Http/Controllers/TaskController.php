<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    protected TaskService $taskService;
    // Injection du Service  Dependency Inversion Principle)
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
        $this->middleware('auth:sanctum');
    }

    public function getAll(Request $request): JsonResponse
    {
        $user = $request->user();
        $tasks = $this->taskService->getTasksForUser($user);

        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $user = $request->user();
        $task = $this->taskService->createTask($user, $request->validated());

        return response()->json($task, 201);
    }


    public function getOne(Task $task): JsonResponse
    {
        if (Auth::user()->id !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized. You do not own this task.'], 403);
        }

        return response()->json($task, 200);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        if (Auth::user()->id !== $task->user_id) {
            abort(403, 'Unauthorized. You do not own this task.');
        }
        
        $updatedTask = $this->taskService->updateTask($task, $request->validated());

        return response()->json($updatedTask);
    }
    
    public function deleteTask(Task $task): Response|JsonResponse
    {
        if (Auth::user()->id !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized. You do not own this task.'], 403);
        }

        $this->taskService->deleteTask($task);

        return response()->noContent();
    }
}