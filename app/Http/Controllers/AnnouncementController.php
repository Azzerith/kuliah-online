<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get announcements for classes the user is in plus general announcements
        $announcements = Announcement::where(function($query) use ($user) {
                $query->whereNull('class_id') // General announcements
                    ->orWhereIn('class_id', $user->classes()->pluck('classes.id')); // Class announcements
            })
            ->with(['class', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($announcements);
    }

    public function store(Request $request, ClassModel $class = null)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_pinned' => 'boolean'
        ]);

        $announcement = Announcement::create(array_merge($validated, [
            'class_id' => $class ? $class->id : null,
            'created_by' => $request->user()->id
        ]));

        // Attach recipients if it's a class announcement
        if ($class) {
            $recipients = $class->members()->pluck('user_id');
            $announcement->recipients()->attach($recipients);
        }

        return response()->json($announcement, 201);
    }

    public function show(Announcement $announcement)
    {
        return response()->json($announcement->load(['class', 'creator']));
    }

    public function markAsRead(Request $request, Announcement $announcement)
    {
        $announcement->markAsRead($request->user()->id);

        return response()->json(['message' => 'Pengumuman ditandai sebagai telah dibaca']);
    }
}