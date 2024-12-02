<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Jobs\BackupDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Backup/Index');
    }

    public function backup()
    {
        try {
            // Dispatch the backup job
            BackupDatabase::dispatch();

            return response()->json([
                'message' => 'Backup process started. Check back for results.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function listFiles()
    {
        try {
            $backupFiles = Storage::disk('backup')->files();
            $fileDetails = array_map(function ($file) {
                return [
                    'name' => $file,
                    'path' => Storage::disk('backup')->path($file),
                    'created_at' => date('F j, Y', filemtime(Storage::disk('backup')->path($file))),
                ];
            }, $backupFiles);
    
            return response()->json([
                'files' => $fileDetails,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve files: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(Request $request, $fileName)
{
    try {
        $filePath = storage_path("app/backups/{$fileName}");

        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return response()->download($filePath);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}

    
}

