@extends('layouts.app')

@section('page', 'Examination Result')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Student Records'];
    @endphp
@endsection

@section('content')
    <div class="row">
        {{-- result table --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>Examination Result</h4>
                        <a href="{{ route('parent.students.records', $student->id) }}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center bg-dark">
                                    <th class="text-white">#</th>
                                    <th class="text-white">Subject</th>
                                    <th class="text-white">Category</th>
                                    <th class="text-white">Grade</th>
                                    <th class="text-white">Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    @php $first = true; @endphp
                                    @foreach ($result as $item)
                                        <tr>
                                            @if ($first)
                                                <td rowspan="{{ count($result) }}" class="text-center">{{ $loop->parent->iteration }}</td>
                                                <td rowspan="{{ count($result) }}">{{ $item->record->subject->name }}</td>
                                                @php $first = false; @endphp
                                            @endif
                                            <td>{{ config('grades.categories.'.$item->category) }}</td>
                                            <td>{{ $item->grade }}</td>
                                            <td>{{ $item->remarks }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- end result table --}}
    </div>
@endsection

@section('script')
    <script></script>
@endsection