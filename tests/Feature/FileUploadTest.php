<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FileUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_user_can_upload_avatar()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/profile/update', [
            'avatar' => $file,
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'avatar_url',
                        ],
                        'files' => [
                            'avatar' => [
                                'id',
                                'original_name',
                                'file_size',
                                'uploaded_at',
                            ]
                        ]
                    ]
                ]);

        $this->assertDatabaseHas('file_uploads', [
            'user_id' => $user->id,
            'type' => 'avatar',
            'is_active' => true,
        ]);
    }

    public function test_user_can_list_files()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Crear algunos archivos de prueba
        FileUpload::factory()->count(3)->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/files');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'files' => [
                            '*' => [
                                'id',
                                'type',
                                'original_name',
                                'file_size',
                                'created_at',
                            ]
                        ]
                    ]
                ]);
    }

    public function test_user_can_delete_file()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $fileUpload = FileUpload::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete("/api/files/{$fileUpload->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Archivo eliminado exitosamente',
                ]);

        $this->assertDatabaseHas('file_uploads', [
            'id' => $fileUpload->id,
            'is_active' => false,
        ]);
    }
} 