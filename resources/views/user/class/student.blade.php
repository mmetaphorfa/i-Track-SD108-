@extends('layouts.app')

@section('page', 'Student Classroom')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Class Management'];
    @endphp
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/tagify.css') }}">
@endsection

@section('content')
    <div class="row">
        {{-- student classroom --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Add Student to Classroom</h4>
                        <a href="{{ route('classes.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('classes.enrollment', $classroom->id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="studentInputs">
                                    <label class="form-label" for="students">Choose Student <span class="text-danger">*</span></label>
                                    <input class="form-control" id="students" name="students" placeholder="Enter student name" value="{{ old('students') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary" type="submit">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- end student classroom --}}

        {{-- student list --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Student List</h4>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="students-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>MyKID</th>
                                    <th>Full Name</th>
                                    <th>Enrolled At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activeStudents as $key => $student)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>{{ $student->student->student_id }}</td>
                                    <td>{{ $student->student->mykid }}</td>
                                    <td>{{ $student->student->full_name }}</td>
                                    <td>{{ date('d/m/Y, h:i A', strtotime($student->enrolled_at)) }}</td>
                                    <td class="text-center">
                                        <ul class="action justify-content-center gap-3">
                                            <li><a target="_blank" href="{{ route('student.management.edit', ['id' => $student->student->id, 'role' => session('role')]) }}"><i class="icon-pencil-alt text-dark"></i></a></li>
                                            <li><a href="javascript:void(0);" class="btn-delete" data-id="{{ $student->id }}"><i class="icon-trash text-danger"></i></a></li>
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
        {{-- end student list --}}
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/select2/tagify.js') }}"></script>
    <script src="{{ asset('assets/js/select2/tagify.polyfills.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Students selection
            const input = $('#students')[0];
            const whitelist = @json($students);
            const tagify = new Tagify(input, {
                whitelist: whitelist,
                enforceWhitelist: true,
                dropdown: {
                    enabled: 1,
                    maxItems: 5,
                    position: 'tag',
                    closeOnSelect: false
                }
            });
            tagify.on('dropdown:focus', function(e) {
                const hoveredItem = e.detail.item;
                hoveredItem.classList.add('focused');
            });
            tagify.on('dropdown:blur', function(e) {
                const hoveredItem = e.detail.item;
                hoveredItem.classList.remove('focused');
            });

            // Datatable
            $("#students-table").DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
            });

            // Delete button
            $('.btn-delete').click(function () {
                const enrollment_id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure you want to remove the student from this classroom?',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create a dynamic form
                        const form = $('<form>', {
                            method: 'POST',
                            action: '{{ route("classes.remove", ":id") }}'.replace(':id', enrollment_id),
                        });

                        // Add CSRF token
                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}',
                        }));

                        // Add the method input for DELETE
                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_method',
                            value: 'PATCH',
                        }));

                        // Append the form to the body and submit
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection