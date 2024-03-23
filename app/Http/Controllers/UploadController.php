<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadFile(Request $request)
    {
        // dd("ok");
        $request->validate([
            'file' => 'required|mimes:png,jpg,mp4'
        ]);

        $uniqueId = $request->header('X-Unique-ID');

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('upload', $filename, 'public');



        return response()->json([
            'message' => 'file uploaded succesfully',
            'filename' => $filename


        ]);
    }

    public function saveFile(Request $request)
    {

        foreach ($request['files'] as $item) {
            $fileModel = new File();
             $fileModel->unique_id = $request['uniqueId'];
             $fileModel->name = $item['name'];
             $fileModel->path = $item['name'];
             $fileModel->save();
        }


        return response()->json([
            'message' => 'file uploaded succesfully',
            'status' => true


        ]);
    }
}
