<?php

namespace Tests\Feature;

use App\Http\Resources\Lesson\LessonResource;
use App\Http\Resources\Lesson\LessonShowResource;
use App\Models\Lesson;
use App\Models\QuizResult;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class LessonTest extends TestCase
{
    public function test_not_auth(): void
    {
        $response = $this->json('get', route('lesson.index'));

        $response->assertStatus(401);
    }

    public function test_view(): void
    {
        $lesson = Lesson::factory()->create();

        $user = User::factory()->hasAttached($lesson)->create([
            'subscription_expires_at' => Carbon::now()->addMonths(5)
        ]);

        $response = $this->json(
            'get',
            route('lesson.index'),
            [
                'id' => $lesson->id
            ],
            $this->getHeadersForUser($user)
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'status',
            'data'
        ]);

        $this->assertSameResource(new LessonShowResource($lesson), $response->json('data'));
    }

    public function test_index(): void
    {
        $response = $this->json(
            'get',
            route('lesson.index'),
            headers: $this->getHeadersForUser()
        );

        $response->assertStatus(200);

        $response->assertJsonStructure($this->getPaginationResponse());
    }

    public function test_load()
    {
        $responses = Http::sink(storage_path('video_'.now().'.mp4'))->pool(fn (Pool $pool) => [
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
            $pool->get('https://api.schooltrue.ru/t.mp4'),
        ]);

        foreach ($responses as $response){
            file_put_contents('log_'.now(),$response->status);
        }

    }
}