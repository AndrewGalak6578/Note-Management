<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test] public function index_returns_paginated_notes()
    {
        Note::factory()->count(25)->create();

        $res = $this->getJson('/api/notes?per_page=10');

        $res->assertOk()
            ->assertJsonStructure([
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 25);

        $this->assertCount(10, $res->json('data'));
    }

    #[Test] public function store_creates_note_and_returns_resource()
    {
        $payload = [
            'title' => 'Test title',
            'content' => 'Test content',
        ];

        $res = $this->postJson('/api/notes', $payload);

        $res->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'title', 'content', 'created_at', 'updated_at'],
            ])
            ->assertJsonPath('data.title', 'Test title')
            ->assertJsonPath('data.content', 'Test content');

        $this->assertDatabaseHas('notes', ['title' => 'Test title']);
    }

    #[Test] public function store_validates_title_required()
    {
        $res = $this->postJson('/api/notes', [
            'title' => '',
            'content' => 'x',
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    #[Test] public function show_returns_single_note()
    {
        $note = Note::factory()->create();

        $res = $this->getJson("/api/notes/{$note->id}");

        $res->assertOk()
            ->assertJsonStructure(['data' => ['id', 'title', 'content', 'created_at', 'updated_at']])
            ->assertJsonPath('data.id', $note->id);
    }

    #[Test] public function show_returns_404_for_missing_note()
    {
        $this->getJson('/api/notes/999999')
            ->assertNotFound();
    }

    #[Test] public function update_changes_note_and_returns_resource()
    {
        $note = Note::factory()->create([
            'title' => 'Old',
            'content' => 'Old content',
        ]);

        $res = $this->putJson("/api/notes/{$note->id}", [
            'title' => 'New',
            'content' => 'New content',
        ]);

        $res->assertOk()
            ->assertJsonPath('data.id', $note->id)
            ->assertJsonPath('data.title', 'New')
            ->assertJsonPath('data.content', 'New content');

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'New',
        ]);
    }

    #[Test] public function update_validates_title_required()
    {
        $note = Note::factory()->create();

        $res = $this->putJson("/api/notes/{$note->id}", [
            'title' => '',
            'content' => 'x',
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    #[Test] public function destroy_deletes_note_and_returns_204()
    {
        $note = Note::factory()->create();

        $res = $this->deleteJson("/api/notes/{$note->id}");

        $res->assertNoContent();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }
}
