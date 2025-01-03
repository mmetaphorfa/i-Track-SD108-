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
                    <h4>Class List</h4>
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
                                    <th>Total Teachers</th>
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
                                    <td class="text-center">
                                        @php
                                            $teachers = App\Models\ClassroomTeacher::where('classroom_id', $class->id)
                                                ->where('status', 'active')->count();
                                            echo number_format($teachers);
                                        @endphp
                                    </td>
                                    <td class="text-center">
                                        <ul class="action justify-content-center gap-3">
                                            <li><a href="{{ route('classes.teachers.edit', $class->id) }}"><i class="icon-pencil-alt txt-primary"></i></a></li>
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
@endsection

@section('script')
    <script>
        // Subjects datatable
        $(document).ready(function () {
            $("#class-table").DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
            });
        });
    </script>
@endsection