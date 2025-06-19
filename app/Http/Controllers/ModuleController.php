<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleController extends Controller
{
    public function index(ClassModel $class)
    {
        $modules = $class->modules()->with(['creator', 'files'])->get();
        return response()->json($modules);
    }

    public function store(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'meeting_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $module = $class->modules()->create(array_merge($validated, [
            'created_by' => $request->user()->id
        ]));

        return response()->json($module, 201);
    }

    public function show(Module $module)
    {
        return response()->json($module->load(['class', 'creator', 'files']));
    }

    public function update(Request $request, Module $module)
    {
        $this->authorize('update', $module);

        $validated = $request->validate([
            'meeting_number' => 'sometimes|integer|min:1',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string'
        ]);

        $module->update($validated);

        return response()->json($module);
    }

    public function destroy(Module $module)
    {
        $this->authorize('delete', $module);

        // Delete associated files
        foreach ($module->files as $file) {
            Storage::delete($file->file_path);
            $file->delete();
        }

        $module->delete();

        return response()->json(null, 204);
    }

    public function uploadFile(Request $request, Module $module)
    {
        $this->authorize('update', $module);

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'type' => 'required|in:teori,praktikum,video'
        ]);

        $file = $request->file('file');
        $path = $file->store('module_files');

        $moduleFile = $module->files()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'type' => $request->type
        ]);

        return response()->json($moduleFile, 201);
    }
}