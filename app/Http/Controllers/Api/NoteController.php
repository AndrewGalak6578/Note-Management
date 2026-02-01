<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Note\StoreRequest;
use App\Http\Requests\Note\UpdateRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;

/**
 * @group Notes
 *
 * API для управления заметками
 * Все ответы возвращаются в формате JSON.
 */
class NoteController extends Controller
{
    /**
     * Получить список заметок (пагинация)
     *
     * @queryParam page int Номер страницы. Example: 1
     * @queryParam per_page Количество на странице (1-50). Example: 10
     * @response 200 scenario="Успех" {"data":[{"id":1,"title":"...","content":"...","created_at":"...","updated_at":"..."}],"links":{"first":"...","last":"...","prev":null,"next":"..."},"meta":{"current_page":1,"last_page":5,"per_page":10,"total":42}}
     */
    public function index()
    {
        $perPage = (int)request()->query('per_page', 10);
        $perPage = max(1, min($perPage, 50));

        $notes = Note::query()->latest()->paginate($perPage);

        return NoteResource::collection($notes);
    }

    /**
     * Создать заметку.
     *
     * @bodyParam title string required Заголовок. Example: "My note"
     * @bodyParam content string Текст. Example: "Hello"
     * @response 201 scenario="Создано" {"data":{"id":1,"title":"My note","content":"Hello","created_at":"...","updated_at":"..."}}
     * @response 422 scenario="Ошибка валидации" {"message":"The title field is required.","errors":{"title":["The title field is required."]}}
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $note = Note::create($data);

        return response()->json(['data' => new NoteResource($note)], 201);
    }

    /**
     * Получить одну заметку.
     *
     * @urlParam note int required ID заметки. Example: 1
     * @response 404 scenario="Не найдено" {"message":"No query results for model [App\\\\Models\\\\Note] 999"}
     */
    public function show(Note $note)
    {
        return response()->json(['data' => new NoteResource($note)], 200);
    }

    /**
     * Создать заметку.
     *
     * @bodyParam title string required Заголовок. Example: "My note"
     * @bodyParam content string Текст. Example: "Hello"
     * @response 200 scenario="Обновлено" {"data":{"id":1,"title":"My note","content":"Hello","created_at":"...","updated_at":"..."}}
     * @response 422 scenario="Ошибка валидации" {"message":"The title field is required.","errors":{"title":["The title field is required."]}}
     */
    public function update(UpdateRequest $request, Note $note)
    {
        $data = $request->validated();

        $note->update($data);
        $note->fresh();

        return response()->json(['data' => new NoteResource($note)], 200);
    }

    /**
     * Удалить заметку.
     *
     * @urlParam note int required ID заметки. Example: 1
     * @response 204
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return response()->noContent(204);
    }
}
