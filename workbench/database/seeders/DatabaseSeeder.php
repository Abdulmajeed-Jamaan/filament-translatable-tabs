<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Workbench\Database\Factories\PostFactory;
use Workbench\Database\Factories\UserFactory;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = UserFactory::new()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Fully translated
        PostFactory::new()->for($user)->create([
            'title' => ['en' => 'Hello world', 'ar' => 'مرحبا بالعالم'],
            'summary' => ['en' => 'A short summary', 'ar' => 'ملخص قصير'],
            'content' => ['en' => 'Some content.', 'ar' => 'بعض المحتوى.'],
        ]);

        // Missing the Arabic translation, to show the empty badge
        PostFactory::new()->for($user)->create();
    }
}
