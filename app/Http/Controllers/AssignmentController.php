<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(ClassModel $class)
    {
        $assignments = $class->assignments()
            ->with(['module', 'questions.options'])
            ->get();
            
        return response()->json($assignments);
    }

    public function store(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'module_id' => 'nullable|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignment_type' => 'required|in:latihan,laporan,responsi,todo',
            'question_type' => 'nullable|in:pilihan_ganda,essay_singkat,lengkapi_kode,file_upload',
            'due_date' => 'nullable|date',
            'total_points' => 'nullable|numeric',
            'is_published' => 'boolean'
        ]);

        $assignment = $class->assignments()->create(array_merge($validated, [
            'created_by' => $request->user()->id
        ]));

        return response()->json($assignment, 201);
    }

    public function show(Assignment $assignment)
    {
        return response()->json($assignment->load(['class', 'module', 'questions.options']));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $validated = $request->validate([
            'module_id' => 'nullable|exists:modules,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'assignment_type' => 'sometimes|in:latihan,laporan,responsi,todo',
            'question_type' => 'nullable|in:pilihan_ganda,essay_singkat,lengkapi_kode,file_upload',
            'due_date' => 'nullable|date',
            'total_points' => 'nullable|numeric',
            'is_published' => 'boolean'
        ]);

        $assignment->update($validated);

        return response()->json($assignment);
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $assignment->delete();

        return response()->json(null, 204);
    }

    public function publish(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $assignment->update(['is_published' => true]);

        return response()->json(['message' => 'Tugas berhasil dipublikasikan']);
    }

    public function addQuestion(Request $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $validated = $request->validate([
            'question' => 'required|string',
            'points' => 'required|numeric|min:0',
            'answer_key' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.text' => 'required_with:options|string',
            'options.*.is_correct' => 'required_with:options|boolean'
        ]);

        $question = $assignment->questions()->create([
            'question' => $validated['question'],
            'question_order' => $assignment->questions()->count() + 1,
            'answer_key' => $validated['answer_key'] ?? null,
            'points' => $validated['points']
        ]);

        if (isset($validated['options'])) {
            foreach ($validated['options'] as $option) {
                $question->options()->create([
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'],
                    'option_order' => $question->options()->count() + 1
                ]);
            }
        }

        return response()->json($question->load('options'), 201);
    }
}