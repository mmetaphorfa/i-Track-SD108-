@extends('layouts.app')

@section('page', 'Student Records')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Management'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- attendance records --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Attendance Records</h4>
                        <a href="{{ route('parent.students.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="attendance-table">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">#</th>
                                    <th scope="col">Class Name (Grade)</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendances as $attendance)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $attendance->attendance->class->name . ' (' . $attendance->attendance->class->level . ')' }}</td>
                                    <td>{{ config('attendances.'.$attendance->attendance->type) }}</td>
                                    <td>{{ date('d/m/Y', strtotime($attendance->attendance->date)) }}</td>
                                    <td>{!! $attendance->status ? '<span class="badge badge-success">Present</span>' : '<span class="badge badge-danger">Absent</span>' !!}</td>
                                    <td>{{ $attendance->remarks ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- end attendance records --}}

        {{-- academic records --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Academic Records</h4>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive theme-scrollbar">
                        <table class="display" id="academic-table">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">#</th>
                                    <th scope="col">Classroom (Grade)</th>
                                    <th scope="col">Examination</th>
                                    <th scope="col">Year</th>
                                    <th scope="col">Results</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $record['classroom'] }}</td>
                                    <td>{{ config('grades.examinations.'.$record['examination']) }}</td>
                                    <td>{{ $record['year'] }}</td>
                                    <td>
                                        <ul class="action justify-content-center">
                                            <li>
                                                <a href="{{ route('parent.students.results', ['id' => $student->id, 'year' => $record['year'], 'examination' => $record['examination']]) }}"
                                                    class="btn btn-outline-primary btn-sm px-3">
                                                     View
                                                 </a>
                                            </li>
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
        {{-- end academic records --}}
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // Attendance datatable
            $("#attendance-table").DataTable({
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            });

            // Academic datatable
            $("#academic-table").DataTable({
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            });
        });</script>
@endsection
