@extends('layouts.app')

@section('page', 'Subject Details')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Subject Management'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- subject details --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Subject Details</h4>
                        <a href="{{ route('subjects.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('subjects.update', $subject->id) }}" method="post">
                        @csrf
                        @method('patch')
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="subject_code">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject_code') border-danger @enderror" id="subject_code" name="subject_code" 
                                    value="{{ old('subject_code', $subject->code) }}" maxlength="5" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="subject_name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject_name') border-danger @enderror" id="subject_name" name="subject_name" 
                                    value="{{ old('subject_name', $subject->name) }}" required>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end subject details --}}

        {{-- teacher list --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Teacher List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="teacher-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>NRIC Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teachers as $key => $teacher)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>{{ $teacher->username }}</td>
                                    <td>{{ $teacher->full_name }}</td>
                                    <td>{{ $teacher->email }}</td>
                                    <td>{{ $teacher->phone }}</td>
                                    <td class="text-center">
                                    @if ($teacher->status == 'active')
                                        <span class="badge rounded-pill badge-success">Active</span>
                                    @else
                                        <span class="badge rounded-pill badge-danger">Inactive</span>
                                    @endif
                                    </td>
                                    <td class="text-center">
                                        <ul class="action">
                                        @if (in_array(session('role'), ['superadmin', 'admin']))
                                            <li class="mx-auto">
                                                <a target="_blank" href="{{ route('teacher.management.edit', ['role' => session('role'), 'id' => $teacher->id]) }}">
                                                    <i class="icon-pencil-alt text-dark"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="mx-auto"><a href="javascript:void(0);"><i class="icon-na text-muted"></i></a></li>
                                        @endif
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
        {{-- end teacher list --}}
    </div>
@endsection

@section('script')
    {{-- teachers datatable --}}
    <script>
        $(document).ready(function () {
            $("#teacher-table").DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
            });
        });
    </script>
@endsection