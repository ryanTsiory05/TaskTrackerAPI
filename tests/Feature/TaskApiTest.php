<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Laravel\Sanctum\Sanctum; // Simulate authentification

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser(): User
    {
        $user = User::factory()->create();
        
        Sanctum::actingAs($user);
        
        return $user;
    }

    /** @test */
    public function authenticated_user_can_create_a_task()
    {
        $user = $this->authenticateUser(); // Authentification

        $taskData = [
            'title' => 'Rédiger le rapport A',
            'description' => 'Finaliser la section 3',
            'status' => 1
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Rédiger le rapport A']);
        
        $this->assertDatabaseHas('tasks', array_merge($taskData, ['user_id' => $user->id]));
    }

    /** @test */
    public function authenticated_user_can_view_only_their_tasks()
    {
        $user = $this->authenticateUser();
        $otherUser = User::factory()->create();

        Task::factory()->for($user, 'user')->count(2)->create();
        Task::factory()->for($otherUser, 'user')->create(['title' => 'Tâche secrète']);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonCount(2)
                 ->assertDontSee('Tâche secrète');
    }

    /** @test */
    public function user_cannot_view_another_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser, 'user')->create();

        $this->authenticateUser(); 

        $response = $this->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(403)
                 ->assertJsonFragment(['error' => 'Unauthorized. You do not own this task.']);
    }

    /** @test */
    public function owner_can_update_their_task()
    {
        $user = $this->authenticateUser();
        $task = Task::factory()->for($user, 'user')->create(['status' => 0]);

        $response = $this->patchJson('/api/tasks/' . $task->id, [
            'status' => 2,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 2]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 2
        ]);
    }

    /** @test */
    public function owner_can_delete_their_task()
    {
        $user = $this->authenticateUser();
        $task = Task::factory()->for($user, 'user')->create();

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(204); 

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

}