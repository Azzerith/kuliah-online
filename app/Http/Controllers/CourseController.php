<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('programStudi')->get();
        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'description' => 'nullable|string'
        ]);

        // Check unique code within program studi
        if (Course::where('program_studi_id', $validated['program_studi_id'])
            ->where('code', $validated['code'])
            ->exists()) {
            return response()->json(['message' => 'Kode mata kuliah sudah ada di program studi ini'], 400);
        }

        $course = Course::create($validated);

        return response()->json($course, 201);
    }

    public function show(Course $course)
    {
        return response()->json($course->load('programStudi'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'program_studi_id' => 'sometimes|exists:program_studi,id',
            'code' => 'sometimes|string|max:10',
            'name' => 'sometimes|string|max:255',
            'sks' => 'sometimes|integer|min:1|max:6',
            'semester' => 'sometimes|integer|min:1|max:8',
            'description' => 'nullable|string'
        ]);

        // Check unique code within program studi if code is being updated
        if (isset($validated['code']) && 
            Course::where('program_studi_id', $validated['program_studi_id'] ?? $course->program_studi_id)
                ->where('code', $validated['code'])
                ->where('id', '!=', $course->id)
                ->exists()) {
            return response()->json(['message' => 'Kode mata kuliah sudah ada di program studi ini'], 400);
        }

        $course->update($validated);

        return response()->json($course);
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return response()->json(null, 204);
    }
}