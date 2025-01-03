<div class="page-header row">
    <div class="header-logo-wrapper col-auto">
        <div class="logo-wrapper">
            <a href="{{ route('user.dashboard', session('role')) }}">
                <img class="img-fluid for-light" src="{{ asset('itrack/images/logo-2.png') }}" alt="logo">
                <img class="img-fluid for-dark" src="{{ asset('itrack/images/logo-2.png') }}" alt="logo">
            </a>
        </div>
    </div>

    <div class="col-4 col-xl-4 page-title">
        <h4 class="f-w-700 mb-lg-2 mb-md-0 mb-2">@yield('page')</h4>
        <nav>
            <ol class="breadcrumb justify-content-sm-start align-items-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('user.dashboard', session('role')) }}"><i data-feather="home"></i></a></li>
                @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item f-w-400">{{ $breadcrumb }}</li>
                @endforeach
                <li class="breadcrumb-item f-w-400 active">@yield('page')</li>
            </ol>
        </nav>
    </div>
    
    <div class="header-wrapper col m-0">
        <div class="row">
            <form class="form-inline search-full col" action="#" method="get">
                <div class="form-group w-100">
                    <div class="Typeahead Typeahead--twitterUsers">
                        <div class="u-posRelative">
                            <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search..." name="s">
                            <div class="spinner-border Typeahead-spinner" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <i class="close-search" data-feather="x"></i>
                        </div>
                        <div class="Typeahead-menu"></div>
                    </div>
                </div>
            </form>
            <div class="header-logo-wrapper col-auto p-0">
                <div class="logo-wrapper">
                    <a href="{{ route('user.dashboard', session('role')) }}"><img class="img-fluid" src="{{ asset('itrack/images/logo.png') }}" alt=""></a>
                </div>
                <div class="toggle-sidebar">
                    <svg class="stroke-icon sidebar-toggle status_toggle middle">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#toggle-icon') }}"></use>
                    </svg>
                </div>
            </div>
            <div class="nav-right col-xxl-8 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
                <ul class="nav-menus align-items-center gap-2">
                    <li>
                        <span class="header-search">
                            <svg>
                                <use href="{{ asset('assets/svg/icon-sprite.svg#search') }}"></use>
                            </svg>
                        </span>
                    </li>
                    <li>
                        <div class="form-group w-100">
                            <div class="Typeahead Typeahead--twitterUsers">
                                <div class="u-posRelative d-flex align-items-center">
                                    <svg class="search-bg svg-color">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#search') }}"></use>
                                    </svg>
                                    <input class="demo-input py-0 Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search..." name="s">
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="fullscreen-body">
                        <span>
                            <svg id="maximize-screen">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#full-screen') }}"></use>
                            </svg>
                        </span>
                    </li>
                    {{-- <li class="onhover-dropdown">
                        <div class="notification-box">
                            <svg>
                                <use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>
                            </svg>
                            <span class="badge rounded-pill badge-primary">4</span>
                        </div>
                        <div class="onhover-show-div notification-dropdown">
                            <h5 class="f-18 f-w-600 mb-0 dropdown-title">Notifications</h5>
                            <ul class="notification-box">
                                <li class="toast default-show-toast align-items-center border-0 fade show" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                                    <div class="d-flex justify-content-between">
                                        <div class="toast-body d-flex p-0">
                                            <div class="flex-shrink-0 bg-light-primary">
                                                <img class="w-auto" src="{{ asset('assets/images/dashboard/icon/wallet.png') }}" alt="Wallet">
                                            </div>
                                            <div class="flex-grow-1">
                                                <a href="private-chat.html">
                                                    <h6 class="m-0">Daily offer added</h6>
                                                </a>
                                                <p class="m-0">User-only offer added</p>
                                            </div>
                                        </div>
                                        <button class="btn-close btn-close-white shadow-none" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li> --}}
                    <li class="profile-nav onhover-dropdown px-0 py-0">
                        <div class="d-flex profile-media align-items-center">
                            <img class="rounded-circle border" src="{{ Storage::disk('public')->url('users/'.Auth::user()->image) }}" 
                                alt="profile-photo" style="width: 35px; height: 35px;">
                            <div class="flex-grow-1">
                                <span class="text-truncate" style="max-width: 150px;">{{ Auth::user()->full_name }}</span>
                                <p class="mb-0 font-outfit">{{ Str::ucfirst(session('role')) }} <i class="fa fa-angle-down"></i></p>
                            </div>
                        </div>
                        <ul class="profile-dropdown onhover-show-div py-1">
                            <li><a href="{{ route('user.profile') }}"><i data-feather="user"></i><span>My Profile</span></a></li>
                            <li><a href="{{ route('user.logout') }}"><i data-feather="log-out"> </i><span>Log Out</span></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>