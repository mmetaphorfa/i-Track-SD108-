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
    <div class="row default-dashboard">
        {{-- welcome --}}
        <div class="col-md-6">
            <div class="card profile-greeting p-0 mb-3">
                <div class="card-body">
                    <div class="img-overlay h-100 d-flex align-items-center justify-content-center">
                        <h1>Hi, {{ Auth::user()->full_name }}!</h1>
                        <p>Welcome to i-Track – simplifying school management for you.</p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-2">
                    <div class="bg-light-primary rounded-3 p-3 pb-2 text-center">
                        <h4 class="fw-semibold mb-3">Quick Links</h4>
                        <div class="d-flex justify-content-center flex-wrap gap-3">
                            <a href="{{ route('user.profile') }}" class="btn btn-primary-gradien mb-2 me-1">Profile</a>
                            <a href="{{ route('parent.students.index') }}" class="btn btn-primary-gradien mb-2 me-1">Students</a>
                            <a href="{{ route('parent.financial.index') }}" class="btn btn-primary-gradien mb-2 me-1">Payments</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end welcome --}}

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
    </script>
@endsection
