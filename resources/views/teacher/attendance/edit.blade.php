@extends('layouts.app')

@section('page', 'Attendance Details')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Attendance Records'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- edit form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Attendance Details</h4>
                        <a href="{{ route('attendances.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('attendances.update', $attendance->id) }}" method="post">
                        @csrf
                        @method('patch')
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="class">Class (Grade)</label>
                                <input type="text" class="form-control" value="{{ $attendance->class->name }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="date">Date</label>
                                <input type="text" class="form-control" value="{{ date('d/m/Y', strtotime($attendance->date)) }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="type">Type</label>
                                <input type="text" class="form-control" value="{{ config('attendances.' . $attendance->type) }}" readonly>
                            </div>
                        </div>
                        <hr>
                        @if ($type == 'new' && $students->count() > 0)
                        <div class="alert alert-light-danger light txt-danger border-left-danger mt-4 mb-3" role="alert">
                            <p class="mb-0">Please click the <b>Save</b> button to keep this attendance record.</p>
                        </div>
                        @endif
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead class="bg-dark text-center">
                                    <tr>
                                        <th class="text-light">#</th>
                                        <th class="text-light">Full Name</th>
                                        <th class="text-light">Status</th>
                                        <th class="text-light">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($students as $key => $student)
                                    <tr>
                                        <td class="text-center" style="width: 5%;">{{ $key + 1 }}</td>
                                        <td>{{ $type == 'recorded' ? $student->student->full_name : $student->full_name }}</td>
                                        <td class="w-25">
                                            <input type="hidden" class="form-control" name="student_id[]" value="{{ $type == 'recorded' ? $student->student->id : $student->id }}" required>
                                            <select class="form-select" name="status[]" id="status" required>
                                                <option value="1" {{ $type == 'recorded' && $student->status == 1 ? 'selected' : '' }}>Present</option>
                                                <option value="0" {{ $type == 'recorded' && $student->status == 0 ? 'selected' : '' }}>Absent</option>
                                            </select>
                                        </td>
                                        <td style="width: 35%;">
                                            <input type="text" class="form-control" name="remarks[]" value="{{ $type == 'recorded' ? $student->remarks : '' }}">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No student available.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($students->count() > 0)
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary mt-2">Save</button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        {{-- end edit form --}}
    </div>
@endsection

@section('script')
    <script></script>
@endsection