<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassModel::with(['course', 'academicPeriod', 'lecturer'])->get();
        return response()->json($classes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'lecturer_id' => 'required|exists:users,id',
            'name' => 'required|string|max:100',
            'meeting_schedule' => 'nullable|string',
            'meeting_link' => 'nullable|url'
        ]);

        $class = ClassModel::create($validated);
        $class->generateClassCode();

        return response()->json($class->load(['course', 'academicPeriod', 'lecturer']), 201);
    }

    public function show(ClassModel $class)
    {
        return response()->json($class->load(['course', 'academicPeriod', 'lecturer', 'members']));
    }

    public function update(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'course_id' => 'sometimes|exists:courses,id',
            'academic_period_id' => 'sometimes|exists:academic_periods,id',
            'lecturer_id' => 'sometimes|exists:users,id',
            'name' => 'sometimes|string|max:100',
            'meeting_schedule' => 'nullable|string',
            'meeting_link' => 'nullable|url',
            'is_active' => 'sometimes|boolean'
        ]);

        $class->update($validated);

        return response()->json($class->load(['course', 'academicPeriod', 'lecturer']));
    }

    public function destroy(ClassModel $class)
    {
        $class->delete();

        return response()->json(null, 204);
    }

    public function join(Request $request)
    {
        $request->validate([
            'class_code' => 'required|string|size:6'
        ]);

        $class = ClassModel::where('class_code', $request->class_code)->firstOrFail();

        // Check if user already joined
        if ($class->members()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Anda sudah bergabung dengan kelas ini'], 400);
        }

        $class->members()->attach($request->user()->id, ['is_asisten' => false]);

        return response()->json(['message' => 'Berhasil bergabung dengan kelas']);
    }

    public function addAssistant(Request $request, ClassModel $class, User $user)
    {
        if ($user->role !== 'mahasiswa') {
            return response()->json(['message' => 'Hanya mahasiswa yang bisa dijadikan asisten'], 400);
        }

        // Check if user is already a member
        if (!$class->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Mahasiswa belum bergabung dengan kelas ini'], 400);
        }

        $class->members()->updateExistingPivot($user->id, ['is_asisten' => true]);

        return response()->json(['message' => 'Mahasiswa berhasil dijadikan asisten']);
    }
}