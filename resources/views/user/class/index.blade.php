@extends('layouts.app')

@section('page', 'Class List')
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
                        <h4>Class List</h4>
                        <a id="addBtn" href="javascript:void(0);" class="btn btn-primary btn-sm">Add New</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="class-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Grade Level</th>
                                    <th>Code</th>
                                    <th>Class Name</th>
                                    <th>Total Students</th>
                                    <th>Assigned Teacher</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classes as $key => $class)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">{{ $class->level }}</td>
                                    <td>{{ $class->code }}</td>
                                    <td>{{ $class->name }}</td>
                                    <td class="text-center">{{ number_format($class->current_limit) . '/' . number_format($class->max_limit) }}</td>
                                    <td>
                                        @if (count($class->teachers) > 0)
                                            @php
                                                $activeTeacher = $class->teachers->first(function ($teacher) {
                                                    return $teacher->pivot->status === 'active';
                                                });
                                            @endphp
                                            {{ $activeTeacher->full_name ?? 'None' }}
                                        @else
                                            None
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <ul class="action justify-content-center gap-3">
                                            <li><a href="{{ route('classes.edit', $class->id) }}"><i class="icon-pencil-alt txt-primary"></i></a></li>
                                            <li><a href="{{ route('classes.students', $class->id) }}"><i class="icon-user txt-warning"></i></a></li>
                                        </ul>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- end class list --}}
    </div>

    {{-- subject modal --}}
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('classes.store') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Class</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="class_code">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('class_code') border-danger @enderror" id="class_code" name="class_code" 
                                    value="{{ old('class_code') }}" maxlength="5" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="class_name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('class_name') border-danger @enderror" id="class_name" name="class_name" 
                                    value="{{ old('class_name') }}" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="grade_level">Grade Level <span class="text-danger">*</span></label>
                                <select name="grade_level" id="grade_level" class="form-select" required>
                                    <option value="">Please Select</option>
                                    <option value="1" {{ old('grade_level') == 1 ? 'selected' : '' }}>Primary 1</option>
                                    <option value="2" {{ old('grade_level') == 2 ? 'selected' : '' }}>Primary 2</option>
                                    <option value="3" {{ old('grade_level') == 3 ? 'selected' : '' }}>Primary 3</option>
                                    <option value="4" {{ old('grade_level') == 4 ? 'selected' : '' }}>Primary 4</option>
                                    <option value="5" {{ old('grade_level') == 5 ? 'selected' : '' }}>Primary 5</option>
                                    <option value="6" {{ old('grade_level') == 6 ? 'selected' : '' }}>Primary 6</option>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="class_limit">Limit (Students in Class) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('class_limit') border-danger @enderror" id="class_limit" name="class_limit" 
                                    value="{{ old('class_limit') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="teacher">Assigned Teacher</label>
                                <select name="teacher" id="teacher" class="form-select">
                                    <option value="">Please Select</option>
                                    @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end subject modal --}}
@endsection

@section('script')
    {{-- trigger modal after submit form --}}
    @if (session()->has('section'))
    <script>
        $(document).ready(function () {
            $('{{ session("section") }}').modal('show');
        });
    </script>
    @endif

    <script>
        // Subjects datatable
        $(document).ready(function () {
            $("#class-table").DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
            });
        });

        // Add modal
        $('#addBtn').on('click', function() {
            $('#createModal').modal('show');
        });
    </script>
@endsection