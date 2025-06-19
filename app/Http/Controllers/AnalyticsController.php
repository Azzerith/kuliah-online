<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\StudentPerformanceMetric;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function classPerformance(ClassModel $class)
    {
        $this->authorize('viewAnalytics', $class);

        $analytics = [
            'total_students' => $class->students()->count(),
            'total_assignments' => $class->assignments()->count(),
            'average_score' => $class->assignments()->with('submissions')->get()
                ->flatMap->submissions
                ->avg('total_score'),
            'completion_rate' => $this->calculateCompletionRate($class),
            'top_performers' => $class->students()
                ->with(['submissions' => function($query) use ($class) {
                    $query->whereIn('assignment_id', $class->assignments()->pluck('id'));
                }])
                ->get()
                ->map(function($student) {
                    return [
                        'student' => $student,
                        'average_score' => $student->submissions->avg('total_score')
                    ];
                })
                ->sortByDesc('average_score')
                ->take(5)
                ->values()
        ];

        return response()->json($analytics);
    }

    public function studentPerformance(ClassModel $class, User $student)
    {
        $this->authorize('viewAnalytics', $class);

        if (!$class->students()->where('user_id', $student->id)->exists()) {
            return response()->json(['message' => 'Mahasiswa tidak terdaftar di kelas ini'], 404);
        }

        $performance = [
            'student' => $student,
            'assignments_completed' => $student->submissions()
                ->whereIn('assignment_id', $class->assignments()->pluck('id'))
                ->count(),
            'assignments_pending' => $class->assignments()->count() - 
                $student->submissions()
                    ->whereIn('assignment_id', $class->assignments()->pluck('id'))
                    ->count(),
            'average_score' => $student->submissions()
                ->whereIn('assignment_id', $class->assignments()->pluck('id'))
                ->avg('total_score'),
            'performance_metrics' => StudentPerformanceMetric::where('student_id', $student->id)
                ->where('class_id', $class->id)
                ->orderBy('calculated_at', 'desc')
                ->get()
        ];

        return response()->json($performance);
    }

    protected function calculateCompletionRate(ClassModel $class)
    {
        $totalAssignments = $class->assignments()->count();
        $totalStudents = $class->students()->count();

        if ($totalAssignments === 0 || $totalStudents === 0) {
            return 0;
        }

        $totalSubmissions = $class->assignments()
            ->withCount('submissions')
            ->get()
            ->sum('submissions_count');

        return ($totalSubmissions / ($totalAssignments * $totalStudents)) * 100;
    }
}