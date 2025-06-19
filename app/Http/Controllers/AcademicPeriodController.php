<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class AcademicPeriodController extends Controller
{
    public function index()
    {
        $periods = AcademicPeriod::orderBy('start_date', 'desc')->get();
        return response()->json($periods);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $period = AcademicPeriod::create($validated);

        return response()->json($period, 201);
    }

    public function show(AcademicPeriod $academicPeriod)
    {
        return response()->json($academicPeriod);
    }

    public function update(Request $request, AcademicPeriod $academicPeriod)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:50',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date'
        ]);

        $academicPeriod->update($validated);

        return response()->json($academicPeriod);
    }

    public function destroy(AcademicPeriod $academicPeriod)
    {
        $academicPeriod->delete();

        return response()->json(null, 204);
    }

    public function activate(AcademicPeriod $academicPeriod)
    {
        // Deactivate all other periods
        AcademicPeriod::where('id', '!=', $academicPeriod->id)->update(['is_active' => false]);
        
        $academicPeriod->update(['is_active' => true]);

        return response()->json(['message' => 'Periode akademik berhasil diaktifkan']);
    }
}