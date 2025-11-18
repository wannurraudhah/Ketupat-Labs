<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    /**
     * Handle file upload
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'sometimes|file|max:10240', // 10MB
            'files.*' => 'sometimes|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $uploadedFiles = [];
        $maxSize = 10 * 1024 * 1024; // 10MB

        try {
            // Handle single file
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                if ($file->getSize() > $maxSize) {
                    return response()->json([
                        'status' => 400,
                        'message' => "File {$file->getClientOriginalName()} exceeds 10MB limit"
                    ], 400);
                }

                $path = $file->store('uploads/' . date('Y/m'), 'public');
                $url = Storage::url($path);

                $uploadedFiles[] = [
                    'url' => $url,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ];
            }

            // Handle multiple files
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    if ($file->getSize() > $maxSize) {
                        return response()->json([
                            'status' => 400,
                            'message' => "File {$file->getClientOriginalName()} exceeds 10MB limit"
                        ], 400);
                    }

                    $path = $file->store('uploads/' . date('Y/m'), 'public');
                    $url = Storage::url($path);

                    $uploadedFiles[] = [
                        'url' => $url,
                        'name' => $file->getClientOriginalName(),
                        'type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ];
                }
            }

            if (empty($uploadedFiles)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No files uploaded or upload error'
                ], 400);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Files uploaded successfully',
                'data' => [
                    'files' => $uploadedFiles
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error("Upload error: " . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to upload files'
            ], 500);
        }
    }
}

