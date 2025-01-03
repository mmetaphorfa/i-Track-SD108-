<div class="sidebar-wrapper" data-layout="stroke-svg">
    <div>
        <div class="logo-wrapper">
            <a href="{{ route('user.dashboard', session('role')) }}">
                <img class="img-fluid" src="{{ asset('itrack/images/logo.png') }}" alt="logo" style="height: 40px; max-width: 130px;">
            </a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar">
                <svg class="stroke-icon sidebar-toggle status_toggle middle">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#toggle-icon') }}"></use>
                </svg>
                <svg class="fill-icon sidebar-toggle status_toggle middle">
                    <use href="{{ asset('assets/svg/icon-sprite.svg#fill-toggle-icon') }}"></use>
                </svg>
            </div>
        </div>
        <div class="logo-icon-wrapper">
            <a href="{{ route('user.dashboard', session('role')) }}">
                <img class="img-fluid" src="{{ asset('itrack/images/favico.png') }}" alt="logo" style="height: 32px;">
            </a>
        </div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn">
                        <a href="{{ route('user.dashboard', session('role')) }}">
                            <img class="img-fluid" src="{{ asset('itrack/images/logo-2.png') }}" alt="">
                        </a>
                        <div class="mobile-back text-end">
                            <span>Back</span> <i class="fa fa-angle-right ps-2" aria-hidden="true"></i>
                        </div>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6>General</h6>
                        </div>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ Route::is('user.dashboard') ? 'is-active' : '' }}"
                            href="{{ route('user.dashboard', session('role')) }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    @if (in_array(session('role'), ['admin', 'teacher']))
                        @if (session('role') == 'admin')
                        <li class="sidebar-main-title">
                            <div>
                                <h6>Management</h6>
                            </div>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title {{ Route::is('*.management.*') ? 'is-active' : '' }}" href="javascript:void(0);">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                                </svg>
                                <span>Users</span>
                            </a>
                            <ul class="sidebar-submenu {{ Route::is('*.management.*') ? 'is-active' : '' }}">
                                @if (session('role') == 'admin' && Auth::user()->username == '999999999999')
                                <li><a class="{{ Route::is('admin.management.*') ? 'is-active' : '' }}" href="{{ route('admin.management.index') }}">Administrators</a></li>
                                @endif
                                @if (session('role') == 'admin')
                                <li><a class="{{ Route::is('teacher.management.*') ? 'is-active' : '' }}" href="{{ route('teacher.management.index', session('role')) }}">Teachers</a></li>
                                @endif
                                <li><a class="{{ Route::is('parent.management.*') ? 'is-active' : '' }}" href="{{ route('parent.management.index', session('role')) }}">Parents</a></li>
                                <li><a class="{{ Route::is('student.management.*') ? 'is-active' : '' }}" href="{{ route('student.management.index', session('role')) }}">Students</a></li>
                            </ul>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title {{ Route::is('classes.*') ? 'is-active' : '' }}" href="javascript:void(0);">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-ui-kits') }}"></use>
                                </svg>
                                <span>Classes</span>
                            </a>
                            <ul class="sidebar-submenu {{ Route::is('classes.*') ? 'is-active' : '' }}">
                                <li><a class="{{ Route::is('classes.index') || Route::is('classes.edit') || Route::is('classes.students') ? 'is-active' : '' }}" href="{{ route('classes.index') }}">Class List</a></li>
                                <li><a class="{{ Route::is('classes.teachers') || Route::is('classes.teachers.edit') ? 'is-active' : '' }}" href="{{ route('classes.teachers') }}">Assigned Teacher</a></li>
                            </ul>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav {{ Route::is('subjects.*') ? 'is-active' : '' }}"
                                href="{{ route('subjects.index') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-learning') }}"></use>
                                </svg>
                                <span>Subjects</span>
                            </a>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav {{ Route::is('financial.*') ? 'is-active' : '' }}"
                                href="{{ route('financial.index') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-landing-page') }}"></use>
                                </svg>
                                <span>Financial</span>
                            </a>
                        </li>
                        <li class="sidebar-main-title">
                            <div>
                                <h6>Portal</h6>
                            </div>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav {{ Route::is('announcements.*') ? 'is-active' : '' }}"
                                href="{{ route('announcements.index') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-layout') }}"></use>
                                </svg>
                                <span>Announcements</span>
                            </a>
                        </li>
                        @endif
                        @if (session('role') == 'teacher')
                        <li class="sidebar-main-title">
                            <div>
                                <h6>Records</h6>
                            </div>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav {{ Route::is('academics.*') ? 'is-active' : '' }}"
                                href="{{ route('academics.index') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-icons') }}"></use>
                                </svg>
                                <span>Academics</span>
                            </a>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title {{ Route::is('attendances.*') ? 'is-active' : '' }}" href="javascript:void(0);">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-to-do') }}"></use>
                                </svg>
                                <span>Attendances</span>
                            </a>
                            <ul class="sidebar-submenu {{ Route::is('attendances.*') ? 'is-active' : '' }}">
                                <li><a class="{{ Route::is('attendances.create') ? 'is-active' : '' }}" href="{{ route('attendances.create') }}">Add Attendance</a></li>
                                <li><a class="{{ Route::is('attendances.index') || Route::is('attendances.edit') ? 'is-active' : '' }}" href="{{ route('attendances.index') }}">Attendance History</a></li>
                            </ul>
                        </li>
                        @endif
                    @else
                    <li class="sidebar-main-title">
                        <div>
                            <h6>Management</h6>
                        </div>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ Route::is('parent.students.*') ? 'is-active' : '' }}"
                            href="{{ route('parent.students.index') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                            </svg>
                            <span>Students</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ Route::is('parent.financial.*') ? 'is-active' : '' }}"
                            href="{{ route('parent.financial.index') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-landing-page') }}"></use>
                            </svg>
                            <span>Financial</span>
                        </a>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6>Support</h6>
                        </div>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ Route::is('parent.faq.index') ? 'is-active' : '' }}"
                            href="{{ route('parent.faq.index') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-chat') }}"></use>
                            </svg>
                            <span>FAQ</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
