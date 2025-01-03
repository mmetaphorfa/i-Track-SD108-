<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SubjectController extends Controller
{
    /* ---------------------------------------
        Subject management page
    --------------------------------------- */
    public function index() {
        $subjects = Subject::with('creator')->orderBy('id', 'desc')->get();
        
        return view('user.subject.index', compact('subjects'));
    }

    /* ---------------------------------------
        Create subject process
    --------------------------------------- */
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'subject_code' => 'required|string|max:5|unique:subjects,code',
                'subject_name' => 'required|string|unique:subjects,name',
            ]);

            // Save new subject
            Subject::create([
                'code' => $validated['subject_code'],
                'name' => $validated['subject_name'],
                'created_by' => Auth::user()->id,
            ]);
            
            return back()->with([
                'result' => 'success',
                'message' => 'New subject has been added.',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return back()->withInput()->withErrors($errors)->with([
                'result' => 'error',
                'message' => $errors->first(),
                'section' => '#createModal',
            ]);
        }
    }

    /* ---------------------------------------
        Subject detais page
    --------------------------------------- */
    public function show(string $id) {
        $subject = Subject::with('creator')->findOrFail($id);

        // Get teacher list
        $teachers = $subject->teachers->filter(function ($teacher) {
            return $teacher->admin_role === 'teacher';
        });
        
        return view('user.subject.show', compact('subject', 'teachers'));
    }

    /* ---------------------------------------
        Update subject process
    --------------------------------------- */
    public function update(Request $request, int $id) {
        $subject = Subject::findOrFail($id);

        try {
            $validated = $request->validate([
                'subject_code' => 'required|string|max:5',
                'subject_name' => 'required|string',
            ]);

            // Check if the new subject code already exists for a different subject
            if (Subject::where('code', $validated['subject_code'])->whereNot('id', $subject->id)->exists()) {
                return back()->withInput()->withErrors(['username' => true])->with([
                    'result' => 'error',
                    'message' => 'The subject code has already been taken.',
                ]);
            }

            // Check if the new subject name already exists for a different subject
            if (Subject::where('code', $validated['subject_name'])->whereNot('id', $subject->id)->exists()) {
                return back()->withInput()->withErrors(['username' => true])->with([
                    'result' => 'error',
                    'message' => 'The subject name has already been taken.',
                ]);
            }

            // Save the subject
            $subject->update([
                'code' => $validated['subject_code'],
                'name' => $validated['subject_name'],
            ]);
            
            return back()->with([
                'result' => 'success',
                'message' => 'The subject has been updated.',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return back()->withInput()->withErrors($errors)->with([
                'result' => 'error',
                'message' => $errors->first(),
            ]);
        }
    }
}
