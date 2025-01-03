@extends('layouts.app')

@section('page', 'Student Details')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Management'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- student details --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Student Details</h4>
                        <a href="{{ route('parent.students.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="row">
                        <div class="col-lg-4 col-md-5 mb-3">
                            <label class="form-label" for="mykid_number">MyKID Number</label>
                            <input type="text" class="form-control" name="mykid_number" value="{{ $student->mykid }}" readonly>
                        </div>
                        <div class="col-lg-8 col-md-7 mb-3">
                            <label class="form-label" for="full_name">Full Name</label>
                            <input type="text" class="form-control" name="full_name" value="{{ $student->full_name }}" readonly>
                        </div>
                        <div class="col-lg-4 col-md-5 mb-3">
                            <label class="form-label" for="student_id">Student ID</label>
                            <input type="text" class="form-control" name="student_id" value="{{ $student->student_id }}" readonly>
                        </div>
                        <div class="col-lg-8 col-md-7 mb-3">
                            <label class="form-label" for="email_address">Email Address</label>
                            <input type="text" class="form-control" name="email_address" value="{{ $student->email }}" readonly>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label" for="dob">Date of Birth</label>
                            <input type="text" class="form-control" name="dob" value="{{ date('d/m/Y', strtotime($student->dob)) }}" readonly>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label" for="gender">Gender</label>
                            <input type="text" class="form-control" name="gender" value="{{ ucfirst($student->gender) }}" readonly>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label" for="race">Race</label>
                            <input type="text" class="form-control" name="race" value="{{ config('student_data.race.'.$student->race) }}" readonly>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label" for="religion">Religion</label>
                            <input type="text" class="form-control" name="religion" value="{{ config('student_data.religion.'.$student->religion) }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="created_at">Created At</label>
                            <input type="text" class="form-control" id="created_at" name="created_at" value="{{ $student->created_at->format('d/m/Y, h:i A') }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="updated_at">Updated At</label>
                            <input type="text" class="form-control" id="updated_at" name="updated_at" value="{{ $student->updated_at->format('d/m/Y, h:i A') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end student details --}}

        {{-- class details --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Class Details</h4>
                </div>
                <div class="card-body py-4">
                    <div class="row">
                        @if ($classroom)
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="class_code">ClassCode</label>
                            <input type="text" class="form-control" id="class_code" name="class_code" value="{{ $classroom->code }}" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label" for="grade_level">Grade Level</label>
                            <input type="text" class="form-control" id="grade_level" name="grade_level" value="{{ $classroom->level }}" readonly>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label" for="class_name">Class Name</label>
                            <input type="text" class="form-control" id="class_name" name="class_name" value="{{ $classroom->name }}" readonly>
                        </div>
                        @else
                        <div class="col-12 text-center">
                            <p class="text-muted">There are currently no class assigned to this student.</p>
                        </div>
                        @endif
                    </div>
                    <hr class="mt-1">
                    <div class="row">
                        <h5 class="fw-semibold mb-2">Previous Classes</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="bg-dark text-center">
                                        <th class="text-light">#</th>
                                        <th class="text-light">Class Code</th>
                                        <th class="text-light">Grade Level</th>
                                        <th class="text-light">Class Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($classrooms as $class)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $class->code }}</td>
                                        <td>{{ $class->level }}</td>
                                        <td>{{ $class->name }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No data found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end class details --}}

        {{-- class timetable --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Class Timetable</h4>
                </div>
                <div class="card-body py-4">
                    @if ($timetable)
                    <img src="{{ Storage::disk('public')->url('timetables/'.$timetable->file_name) }}" alt="" class="img-fluid border rounded">
                    @else
                    <div class="text-center">
                        <p class="text-muted">There are currently no timetable assigned to this class.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- end class timetable --}}
    </div>
@endsection

@section('script')
    <script></script>
@endsection