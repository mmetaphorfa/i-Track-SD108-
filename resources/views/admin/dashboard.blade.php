@extends('layouts.app')

@section('page', 'Dashboard')
@section('breadcrumbs')
    @php
        $breadcrumbs = [];
    @endphp
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/calendar.css') }}">
@endsection

@section('content')
    <div class="row">
        {{-- user statistics --}}
        @foreach ($totalUsers as $key => $total)
        <div class="col-lg-3 col-md-6">
            <a href="{{ $total['url'] }}" class="card mb-4">
                <div class="card-body px-3 {{ $key == 0 ? 'total-sells' : 'total-sells-'.$key + 1 }}">
                    <div class="d-flex align-items-center gap-2"> 
                        <div class="flex-shrink-0"><i class="icon-{{ $total['icon'] }} text-light fw-bold fs-5"></i></div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2"> 
                                <h2>{{ number_format($total['value']) }}</h2>
                            </div>
                            <p class="w-100">{{ $total['name'] }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
        {{-- end user statistics --}}

        {{-- latest payment --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4 class="">5 Latest Payments</h4>
                        <a href="{{ route('financial.index') }}" class="txt-primary">View All</a>
                    </div>
                </div>
                <div class="card-body sales-product px-0 pb-0">
                    <div class="table-responsive theme-scrollbar">
                        <table class="table display" style="width:100%">
                        <thead style="background: rgba(0, 0, 0, 0.05);">
                            <tr>
                                <th>Full Name</th>
                                <th>Amount</th>
                                <th>Paid At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                            <tr>
                                <td>{{ $payment->full_name }}</td>
                                <td>RM {{ $payment->amount }}</td>
                                <td>{{ date('d/m/Y, h:i A', strtotime($payment->paid_at)) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                  </div>
                </div>
            </div>
        </div>
        {{-- end latest payment --}}

        {{-- latest studenets --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h4>5 Latest Students</h4>
                        <a href="{{ route('student.management.index', session('role')) }}" class="txt-primary">View All</a>
                    </div>
                </div>
                <div class="card-body sales-product px-0 pb-0">
                    <div class="table-responsive theme-scrollbar">
                        <table class="table display" style="width:100%">
                            <thead style="background: rgba(0, 0, 0, 0.05);">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Full Name</th>
                                    <th>Parent Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student)
                                <tr>
                                    <td>{{ $student->student_id }}</td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->parent->full_name }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No data found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- end latest payment --}}

        {{-- calendar --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
        {{-- end calendar --}}
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/calendar/fullcalendar.min.js') }}"></script>
    <script>
        // Initialize the calendar
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth',
                },
                initialView: 'dayGridMonth',
                nowIndicator: true,
                events: @json($announcements),
            });
            calendar.render();
        });
    </script>
@endsection