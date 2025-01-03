@extends('layouts.app')

@section('page', 'Student List')
@section('breadcrumbs')
    @php
        $breadcrumbs = ['Management'];
    @endphp
@endsection

@section('content')
    <div class="row">
        @forelse ($students as $student)
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-lg-3 col-4">
                            <div class="d-block bg-light-dark rounded pt-2" style="aspect-ratio: 1/1 !important;">
                                <img src="{{ asset('itrack/images/'.$student->gender.'-student.png') }}" alt="" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-lg-9 col-8">
                            <div class="py-1">
                                <h5 class="txt-primary fw-bold mb-2">{{ $student->full_name }}</h5>
                                <p class="text-muted mb-lg-3 mb-2">{{ $student->mykid }}</p>
                                <div class="d-flex align-items-center justify-content-end" style="gap: 6px;">
                                    <a href="{{ route('parent.students.records', $student->id) }}" class="btn btn-warning text-white py-1 px-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Student Records"><small><i class="icon-write"></i></small></a>
                                    <a href="{{ route('parent.students.show', $student->id) }}" class="btn btn-success py-1 px-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Student Details"><small><i class="icon-search"></i></small></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <h3>There are currently no students.</h3>
        </div>
        @endforelse
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
    <script></script>
@endsection