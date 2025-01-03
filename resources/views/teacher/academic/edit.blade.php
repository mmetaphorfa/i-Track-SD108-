@extends('layouts.app')

@section('page', 'Edit Records')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Academics'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- edit form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    @php
                        $backId = App\Models\ClassroomTeacher::where('subject_id', $record->subject_id)->where('classroom_id', $record->classroom_id)
                            ->first();
                    @endphp
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Record Details</h4>
                        <a href="{{ route('academics.show', $backId->id) }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('academics.update', $record->id) }}" method="post">
                        @csrf
                        @method('patch')
                        @if ($type == 'new' && $students->count() > 0)
                        <div class="alert alert-light-danger light txt-danger border-left-danger mb-3" role="alert">
                            <p class="mb-0">Please click the <b>Save</b> button to keep this academic record.</p>
                        </div>
                        @endif
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead class="bg-dark text-center">
                                    <tr>
                                        <th class="text-light">#</th>
                                        <th class="text-light">Full Name</th>
                                        <th class="text-light">Category</th>
                                        <th class="text-light">Grades</th>
                                        <th class="text-light">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($students as $key => $student)
                                        <tr>
                                            <td class="text-center" style="width: 7%;" rowspan="5">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td rowspan="5">
                                                {{ $student->student->full_name }}
                                                <input type="hidden" class="form-control" name="student_id[]" value="{{ $student->student->id }}" required>
                                            </td>
                                        </tr>
                                        @foreach (config('grades.categories') as $cat_key => $category)
                                            <tr>
                                                <td>{{ $category }}</td>
                                                <td>
                                                    <select class="form-select" name="grades[{{ $key }}][]" required>
                                                        <option value="">Please Select</option>
                                                        @foreach (config('grades.grades') as $grade)
                                                            @php
                                                                $isExists = App\Models\Grade::where('record_id', $record->id)
                                                                    ->where('student_id', $student->student->id)
                                                                    ->where('category', $cat_key)->first();
                                                            @endphp
                                                            <option value="{{ $grade }}" 
                                                                {{ old('grades.'.$key.'.'.($cat_key-1), $isExists->grade ?? '') == $grade ? 'selected' : '' }}>
                                                                {{ $grade }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="hidden" class="form-control" name="category[{{ $key }}][]" value="{{ $cat_key }}" required>
                                                    <input type="text" class="form-control" name="remarks[{{ $key }}][]" value="{{ old('remarks.'.$key.'.'.($cat_key-1), $isExists->remarks ?? '') }}" required>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No student found</td>
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