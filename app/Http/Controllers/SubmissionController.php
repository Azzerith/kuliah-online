<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index(Assignment $assignment)
    {
        $submissions = $assignment->submissions()
            ->with(['student', 'answers'])
            ->get();
            
        return response()->json($submissions);
    }

    public function show(Assignment $assignment, AssignmentSubmission $submission)
    {
        return response()->json($submission->load(['student', 'answers.question']));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        
        // Check if already submitted
        if ($assignment->submissions()->where('student_id', $user->id)->exists()) {
            return response()->json(['message' => 'Anda sudah mengumpulkan tugas ini'], 400);
        }

        $submission = $assignment->submissions()->create([
            'student_id' => $user->id,
            'submission_status' => 'submitted',
            'submitted_at' => now()
        ]);

        return response()->json($submission, 201);
    }

    public function submitAnswer(Request $request, Assignment $assignment, $questionId)
    {
        $user = $request->user();
        $question = $assignment->questions()->findOrFail($questionId);

        $validated = $request->validate([
            'answer' => 'nullable|string',
            'file' => 'nullable|file|max:5120' // Max 5MB
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignment_answers');
        }

        $answer = StudentAnswer::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'question_id' => $question->id,
                'student_id' => $user->id
            ],
            [
                'answer' => $validated['answer'] ?? null,
                'file_path' => $filePath,
                'submitted_at' => now()
            ]
        );

        return response()->json($answer);
    }

    public function gradeAnswer(Request $request, StudentAnswer $answer)
    {
        $this->authorize('grade', $answer);

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:'.$answer->question->points,
            'feedback' => 'nullable|string'
        ]);

        $answer->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'corrected_by' => $request->user()->id,
            'corrected_at' => now()
        ]);

        // Update submission total score
        $submission = $answer->assignment->getStudentSubmission($answer->student_id);
        if ($submission) {
            $submission->calculateTotalScore();
        }

        return response()->json($answer);
    }
}