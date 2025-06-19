<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\TodoList;
use App\Models\TodoProgress;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(ClassModel $class)
    {
        $todos = $class->todoLists()->with(['creator', 'progress'])->get();
        return response()->json($todos);
    }

    public function store(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'meeting_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $todo = $class->todoLists()->create(array_merge($validated, [
            'created_by' => $request->user()->id
        ]));

        return response()->json($todo, 201);
    }

    public function show(TodoList $todo)
    {
        return response()->json($todo->load(['class', 'creator', 'progress.student']));
    }

    public function update(Request $request, TodoList $todo)
    {
        $this->authorize('update', $todo);

        $validated = $request->validate([
            'meeting_number' => 'sometimes|integer|min:1',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string'
        ]);

        $todo->update($validated);

        return response()->json($todo);
    }

    public function destroy(TodoList $todo)
    {
        $this->authorize('delete', $todo);

        $todo->delete();

        return response()->json(null, 204);
    }

    public function updateProgress(Request $request, TodoList $todo)
    {
        $user = $request->user();

        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed',
            'notes' => 'nullable|string'
        ]);

        $progress = $todo->progress()->updateOrCreate(
            ['student_id' => $user->id],
            [
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'completed_at' => $validated['status'] === 'completed' ? now() : null
            ]
        );

        return response()->json($progress);
    }

    public function verifyProgress(Request $request, TodoProgress $progress)
    {
        $this->authorize('verify', $progress);

        $progress->verify($request->user()->id);

        return response()->json(['message' => 'Progress berhasil diverifikasi']);
    }
}