@extends('layouts.app')

@section('page', 'Dashboard')
@section('breadcrumbs')
    @php
        $breadcrumbs = [];
    @endphp
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/calendar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@endsection

@section('content')
    <div class="row">
        {{-- annoucements --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Announcements</h4>
                </div>
                <div class="card-body">
                    @if ($announcements->count() > 0)
                    <div class="swiper custom-swiper-1">
                        <div class="swiper-wrapper">
                            @foreach ($announcements as $announcement)
                                <div class="swiper-slide">
                                    @if ($announcement->thumbnail)
                                    <img src="{{ Storage::url('thumbnails/'.$announcement->thumbnail) }}" class="img-fluid rounded border mb-3" alt="{{ $announcement->title }}">
                                    @endif
                                    <div class="bg-light text-dark rounded p-3">
                                        <h3 class="fw-semibold mb-3"><u>{{ $announcement->title }}</u></h3>
                                        <p><b>Date: </b>{{ date('d/m/Y', strtotime($announcement->start_at)) . ' - ' . date('d/m/Y', strtotime($announcement->end_at)) }}</p>
                                        <p class="description">{!! nl2br(e($announcement->description)) !!}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-1 mt-3">
                        <div class="btn btn-primary button-prev"><i class="fa fa-arrow-left"></i></div>
                        <div class="btn btn-primary button-next"><i class="fa fa-arrow-right"></i></div>
                    </div>
                    @else
                    <div class="text-center">
                        <p>There are currently no updates or announcements</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- end announcements --}}

        {{-- statistics --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>My Classes</h4>
                </div>
                <div class="card-body pt-3 pb-4">
                    @if ($classrooms->count() > 0)
                    <canvas id="class-chart" class="w-100 h-100"></canvas>
                    @else
                    <div class="text-center">
                        <p>There are currently no classes assigned to you</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- end statistics --}}

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
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                events: @json($events),
            });
            calendar.render();
        });

        // Initialize swiper
        const swiper = new Swiper('.swiper', {
            loop: true,
            spaceBetween: 10,
            autoplay: {
                delay: 5000,
            },
            navigation: {
                nextEl: '.button-next',
                prevEl: '.button-prev',
            },
        });

        // Initialize chart
        const classrooms = @json($classrooms);
        const labels = classrooms.map(item => item.classroom);
        const dataPoints = classrooms.map(item => item.students);

        // Define colors
        const colors = [
            'rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)',
            'rgb(75, 192, 192)', 'rgb(153, 102, 255)', 'rgb(255, 159, 64)',
            'rgb(201, 203, 207)', 'rgb(255, 99, 71)', 'rgb(60, 179, 113)',
            'rgb(123, 104, 238)'
        ];

        // Chart data
        const data = {
            labels: labels,
            datasets: [{
                label: 'Total Students',
                data: dataPoints,
                backgroundColor: colors.slice(0, labels.length),
                hoverOffset: 4
            }],
        };
        const ctx = document.getElementById('class-chart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: data,
        });
    </script>
@endsection
