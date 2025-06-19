<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    public function index()
    {
        $programStudis = ProgramStudi::all();
        return response()->json($programStudis);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:program_studi',
            'jenjang' => 'required|in:D3,S1,S2,S3',
            'fakultas' => 'required|string|max:100'
        ]);

        $prodi = ProgramStudi::create($validated);

        return response()->json($prodi, 201);
    }

    public function show(ProgramStudi $programStudi)
    {
        return response()->json($programStudi);
    }

    public function update(Request $request, ProgramStudi $programStudi)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:10|unique:program_studi,code,'.$programStudi->id,
            'jenjang' => 'sometimes|in:D3,S1,S2,S3',
            'fakultas' => 'sometimes|string|max:100'
        ]);

        $programStudi->update($validated);

        return response()->json($programStudi);
    }

    public function destroy(ProgramStudi $programStudi)
    {
        $programStudi->delete();

        return response()->json(null, 204);
    }
}