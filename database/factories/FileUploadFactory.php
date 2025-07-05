<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FileUpload>
 */
class FileUploadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['avatar', 'document', 'exercise_video', 'exercise_image']),
            'original_name' => $this->faker->fileName(),
            'file_name' => $this->faker->uuid() . '.jpg',
            'file_path' => 'avatars/' . $this->faker->uuid() . '.jpg',
            'public_url' => '/storage/avatars/' . $this->faker->uuid() . '.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(1000, 5000000),
            'disk' => 'public',
            'metadata' => [
                'width' => $this->faker->numberBetween(100, 1920),
                'height' => $this->faker->numberBetween(100, 1080),
                'aspect_ratio' => $this->faker->randomFloat(2, 0.5, 2.0),
                'original_extension' => 'jpg',
                'uploaded_at' => now()->toISOString(),
            ],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the file is an avatar.
     */
    public function avatar(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'avatar',
            'mime_type' => 'image/jpeg',
            'file_path' => 'avatars/' . $this->faker->uuid() . '.jpg',
            'public_url' => '/storage/avatars/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Indicate that the file is a document.
     */
    public function document(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'document',
            'mime_type' => 'application/pdf',
            'file_path' => 'documents/' . $this->faker->uuid() . '.pdf',
            'public_url' => '/storage/documents/' . $this->faker->uuid() . '.pdf',
        ]);
    }

    /**
     * Indicate that the file is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
} 