<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AnnouncementController extends Controller
{
    /* ---------------------------------------
        Announcement management page
    --------------------------------------- */
    public function index() {
        $news = Announcement::with('creator')->orderBy('created_at', 'desc') ->get();

        return view('user.announcement.index', compact('news'));
    }

    /* ---------------------------------------
        Create announcement page
    --------------------------------------- */
    public function create() {
        return view('user.announcement.create');
    }

    /* ---------------------------------------
        Create announcement process
    --------------------------------------- */
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'thumbnail' => 'nullable|file|image|mimes:jpeg,png,jpg|max:5120',
                'title' => 'required|string',
                'start_at' => 'required|date|before:end_at',
                'end_at' => 'required|date|after:start_at',
                'description' => 'required|string',
                'status' => 'nullable|boolean',
            ]);

            // Check if status checkbox checked
            $status = !empty($validated['status']) ? 'draft' : 'published';

            DB::beginTransaction();
            try {
                // Save new announcement
                $news = Announcement::create([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'start_at' => $validated['start_at'],
                    'end_at' => $validated['end_at'],
                    'created_by' => Auth::user()->id,
                    'status' =>  $status,
                ]);

                // Check if a file was uploaded
                if ($request->hasFile('thumbnail')) {
                    $file = $request->file('thumbnail');

                    // Upload path in `public/storage/thumbnails`
                    $uploadPath = 'thumbnails';

                    // Create folder if does not exists
                    if (!Storage::disk('public')->exists($uploadPath)) {
                        Storage::disk('public')->makeDirectory($uploadPath);
                    }

                    // Store the file with a unique name
                    $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs($uploadPath, $fileName, 'public');

                    // Save file name to database
                    $news->thumbnail = $fileName;
                    $news->save();
                }
                
                DB::commit();
                return to_route('announcements.index')->with([
                    'result' => 'success',
                    'message' => 'New announcement has been created.',
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

    /* ---------------------------------------
        Announcement details page
    --------------------------------------- */
    public function edit(string $id) {
        $news = Announcement::with('creator')->findOrFail($id);
        
        return view('user.announcement.edit', compact('news'));
    }

    /* ---------------------------------------
        Update announcement process
    --------------------------------------- */
    public function update(Request $request, string $id) {
        $news = Announcement::with('creator')->findOrFail($id);

        try {
            $validated = $request->validate([
                'thumbnail' => 'nullable|file|image|mimes:jpeg,png,jpg|max:5120',
                'title' => 'required|string',
                'start_at' => 'required|date|before:end_at',
                'end_at' => 'required|date|after:start_at',
                'description' => 'required|string',
                'status' => 'nullable|boolean',
            ]);

            // Check if status checkbox checked
            $status = !empty($validated['status']) ? 'draft' : 'published';

            DB::beginTransaction();
            try {
                // Save new announcement
                $news->update([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'start_at' => $validated['start_at'],
                    'end_at' => $validated['end_at'],
                    'status' =>  $status,
                ]);

                // Check if a file was uploaded
                if ($request->hasFile('thumbnail')) {
                    $file = $request->file('thumbnail');

                    // Upload path in `public/storage/thumbnails`
                    $uploadPath = 'thumbnails';

                    // Store the file with a unique name
                    $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs($uploadPath, $fileName, 'public');

                    // Save file name to database
                    $news->thumbnail = $fileName;
                    $news->save();
                }
                
                DB::commit();
                return to_route('announcements.index')->with([
                    'result' => 'success',
                    'message' => 'The announcement has been updated.',
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

    /* ---------------------------------------
        Delete announcement process
    --------------------------------------- */
    public function destroy(string $id) {
        $news = Announcement::findOrFail($id);
        $news->delete();

        return to_route('announcements.index')->with([
            'result' => 'success',
            'message' => 'The announcement has been deleted.',
        ]);
    }
}
