@extends('layouts.app')

@section('page', 'My Classes')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Academics'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- datatable --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>My Classes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="academics-table">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Grade</th>
                                    <th>Class Code</th>
                                    <th>Class Name</th>
                                    <th>Total Students</th>
                                    <th>Subject Teach</th>
                                    <th>Academic Records</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classes as $class)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $class->classroom->level }}</td>
                                        <td>{{ $class->classroom->code }}</td>
                                        <td>{{ $class->classroom->name }}</td>
                                        <td>{{ $class->classroom->current_limit . '/' . $class->classroom->max_limit }}</td>
                                        <td>{{ $class->subject->name }}</td>
                                        <td class="text-center">
                                            <ul class="action justify-content-center gap-3">
                                                <li><a href="{{ route('academics.show', $class->id) }}"><i class="icon-pencil-alt txt-dark"></i></a></li>
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
        {{-- end datatable --}}
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // Academics datatable
            $("#academics-table").DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
            });
        });
    </script>
@endsection
