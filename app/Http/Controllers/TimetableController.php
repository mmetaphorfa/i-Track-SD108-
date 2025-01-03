<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TimetableController extends Controller
{
    /* ---------------------------------------
        Upload timetable process
    --------------------------------------- */
    public function upload(Request $request, int $id) {
        try {
            // Validate the uploaded file
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            // Define the directory path
            $directory = 'timetables';

            // Check if the directory exists, create it if not
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            DB::beginTransaction();
            try {
                // Generate a unique filename with the original extension
                $file = $request->file('image');
                $filename = uniqid(true) . '.' . $file->getClientOriginalExtension();

                // Save the file with the unique name
                if ($file->storeAs($directory, $filename, 'public')) {
                    // Set the previous timetable to inactive
                    $currentTimetable = Timetable::where('class_id', $id)->where('status', 'active')->first();
                    if ($currentTimetable) {
                        $currentTimetable->update([
                            'status' => 'inactive',
                        ]);
                    }

                    // Create new timetable data
                    Timetable::create([
                        'class_id' => $id,
                        'file_name' => $filename,
                        'upload_by' => Auth::user()->id,
                    ]);
                }
                
                DB::commit();
                return back()->with([
                    'result' => 'success',
                    'message' => 'The timetable has been uploaded.',
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'An error occurred while processing your request. Please try again.',
                ]);
            }
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return back()->withInput()->withErrors($errors)->with([
                'result' => 'error',
                'message' => $errors->first(),
            ]);
        }
    }
}
