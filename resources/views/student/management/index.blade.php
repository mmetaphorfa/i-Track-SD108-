@extends('layouts.app')

@section('page', 'Student List')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Users Management'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- datatable --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Student List</h4>
                        <a href="{{ route('student.management.create', session('role')) }}" class="btn btn-primary btn-sm">Add New</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="student-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>MyKID</th>
                                    <th>Full Name</th>
                                    <th>Class (Grade)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- end datatable --}}
    </div>
@endsection

@section('script')
    {{-- users datatable --}}
    <script>
        $(document).ready(function () {
            $("#student-table").DataTable({
                pageLength: 50,
                lengthMenu: [50, 100, 150, 200],
                ajax: {
                    url: "{{ route('ajax.getStudentData', session('role')) }}",
                    type: 'get',
                    dataSrc: function (json) {
                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                        console.log(xhr.responseText);
                    }
                },
                columns: [
                    { data: 'num', className: 'text-center', searchable: false },
                    { data: 'student_id' },
                    { data: 'mykid' },
                    { data: 'name' },
                    { data: 'class' },
                    { 
                        data: 'id', orderable: false, searchable: false, 
                        render: function(data, type, row) {
                            let url = '{{ route("student.management.edit", ["role" => ":role", "id" => ":id"]) }}';
                            url = url.replace(':role', '{{ session("role") }}').replace(':id', data);
                            return '<ul class="action"><li class="mx-auto"><a href="' + url + '"><i class="icon-pencil-alt text-dark"></i></a></li></ul>';
                        }
                    },
                ],
            });
        });
    </script>
@endsection