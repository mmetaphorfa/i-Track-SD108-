@extends('layouts.app')

@section('page', 'Subject List')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Subject Management'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- subject list --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Subject List</h4>
                        <a id="addBtn" href="javascript:void(0);" class="btn btn-primary btn-sm">Add New</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="subject-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Created By</th>
                                    <th>Total Teachers</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjects as $key => $subject)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>{{ $subject->code }}</td>
                                    <td>{{ $subject->name }}</td>
                                    <td>{{ $subject->creator->full_name }}</td>
                                    <td class="text-center">
                                    @php
                                        $teacherCount = $subject->teachers->filter(function ($teacher) {
                                            return $teacher->admin_role === 'teacher';
                                        })->count();
                                    @endphp
                                    @if ($teacherCount === 0)
                                        <span class="badge badge-danger">0</span>
                                    @else
                                        <span class="badge badge-success">{{ $teacherCount }}</span>
                                    @endif
                                    </td>
                                    <td class="text-center">
                                        <ul class="action justify-content-center gap-2">
                                            <li><a href="{{ route('subjects.show', $subject->id) }}"><i class="icon-search text-dark"></i></a></li>
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
        {{-- end subject list --}}
    </div>

    {{-- subject modal --}}
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('subjects.store') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Subject</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="subject_code">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject_code') border-danger @enderror" id="subject_code" name="subject_code" 
                                    value="{{ old('subject_code') }}" maxlength="5" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="subject_name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject_name') border-danger @enderror" id="subject_name" name="subject_name" 
                                    value="{{ old('subject_name') }}" required>
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
            $("#subject-table").DataTable({
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            });
        });

        // Add modal
        $('#addBtn').on('click', function() {
            $('#createModal').modal('show');
        });
    </script>
@endsection