@extends('layouts.app')

@section('page', 'Assigned Teachers')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Class Management'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- class list --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Assigned Teachers</h4>
                        <a href="{{ route('classes.teachers') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="alert alert-light-primary light txt-primary border-left-primary mb-3" role="alert">
                        <p class="mb-0">Select <b>N/A</b> for subjects that are not applicable to this class.</p>
                    </div>
                    <form action="{{ route('classes.teachers.update', $classroom->id) }}" method="post">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-dark text-center">
                                    <tr>
                                        <th class="text-light" style="width: 7%;">#</th>
                                        <th class="text-light">Subject Code</th>
                                        <th class="text-light">Subject Name</th>
                                        <th class="text-light w-50">Teacher</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($subjects as $key => $subject)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td>{{ $subject->code }}</td>
                                        <td>{{ $subject->name }}</td>
                                        <td>
                                            @php
                                                $teachers = $subject->teachers ?? [];
                                                $isAssigned = App\Models\ClassroomTeacher::where('subject_id', $subject->id)
                                                    ->where('classroom_id', $classroom->id)->where('status', 'active')->first() ?? null;
                                            @endphp
                                            <input type="hidden" name="subject_id[]" value="{{ $subject->id }}" required>
                                            <select class="form-select" name="teacher_id[]">
                                                <option value="">N/A</option>
                                                @foreach ($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}" {{ $isAssigned && $isAssigned->teacher_id == $teacher->id ? 'selected' : '' }}>
                                                        {{ $teacher->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No data available.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($subjects->isNotEmpty())
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        {{-- end class list --}}
    </div>
@endsection

@section('script')
    <script></script>
@endsection