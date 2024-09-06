<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use getID3 as GlobalGetID3;
use OwenIt\LaravelGetId3\GetId3;
use Owenoj\LaravelGetId3\GetId3 as LaravelGetId3GetId3;

class VideoController extends Controller
{
    public function store(Request $request)
{
    if ($request->hasFile('video')) {
        $video = $request->file('video');
        $filePath = $video->store('videos');

        // Initialize GetId3
        $getID3 = new GlobalGetID3();
        
        // Analyze the file
        $file = $getID3->analyze(storage_path('app/' . $filePath));

        $duration = $file['playtime_string'] ?? 'Duration not available';

        $videoRecord = new Video();
        $videoRecord->filename = $video->getClientOriginalName();
        $videoRecord->path = $filePath;
        $videoRecord->duration = $duration;
        $videoRecord->save();

        return response()->json(['message' => 'Video uploaded and information saved', 'data' => $videoRecord]);
    }

    return response()->json(['error' => 'No video file provided'], 400);
}

}
