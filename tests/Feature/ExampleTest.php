<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * A basic test example.
     */
    public function test_the_create_user_feature(): void
    {
        $response = $this->json('POST', route('register'), [
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
    }

    /**
     * A basic test example.
     */
    public function test_the_login_user_feature(): void
    {
        $user = User::factory()->create();

        $response = $this->json('POST', route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['name', 'email'], 'token']);

    }

    public function test_create_task_feature(): void
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Test task',
            'description' => 'This is a task description.',
        ];

        $response = $this->actingAs($user)->postJson(route('task.store'), $data);

        $response->assertOk();
        $response->assertStatus(200);

        $response->assertJsonStructure(['success', 'message',
            'data' => [
                'id', 'title', 'description', 'user_id',
            ],
        ])
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('tasks', $data);

    }

    public function test_update_task_feature(): void
    {
        $user = User::factory()->create();

        $original_data = [
            'title' => 'Test task',
            'description' => 'This is a task description.',
        ];

        $data = [
            'title' => 'Test task',
            'description' => 'This is a task description.',
        ];

        $response = $this->actingAs($user)->postJson(route('task.store'), $original_data);

        $id = Arr::get($response->json(), 'data.id');

        $response = $this->actingAs($user)->putJson(route('task.update', ['task' => $id]), $data);

        $response->assertOk();
        $response->assertStatus(200);

        $response->assertJsonStructure(['success', 'message',
            'data' => [
                'id', 'title', 'description', 'user_id',
            ],
        ])
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('tasks', $data);

    }

    public function test_delete_task(): void
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Test task',
            'description' => 'This is a task description.',
        ];

        $response = $this->actingAs($user)->postJson(route('task.store'), $data);

        $id = Arr::get($response->json(), 'data.id');

        $response = $this->actingAs($user)->deleteJson(route('task.destroy', ['task' => $id]));

        $response->assertOk();
        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', [
            'id' => $id,
        ]);
    }

    public function test_user_all_tasks(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('task.index'));

        $response->assertOk();
        $response->assertStatus(200);

        $response->assertJsonStructure(["success",
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'user_id',
                ],
            ],
        ]);
    }

    public function test_user_task(): void
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Test task',
            'description' => 'This is a task description.',
        ];

        $response = $this->actingAs($user)->postJson(route('task.store'), $data);

        $id = Arr::get($response->json(), 'data.id');

        $response = $this->actingAs($user)->getJson(route('task.show', ['task' => $id]));

        $response->assertOk();
        $response->assertStatus(200);

        $response->assertJsonStructure(["success",
            'data' => [
                'id',
                'title',
                'description',
                'user_id',
            ],
        ]);
    }
}
