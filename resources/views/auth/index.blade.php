@extends('layouts.app')

@section('page', 'Log In')

@section('content')
    <div class="row m-0">
        <div class="col-12 p-0">
            <div class="login-card login-dark"
                style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('{{ asset('itrack/images/covers/bg-login.jpg') }}');">
                <div>
                    <div>
                        <a class="logo" href="{{ route('user.index') }}">
                            <img class="img-fluid for-light" src="{{ asset('itrack/images/logo-login.png') }}" alt="logo" style="height: 40px; width: auto; max-width: 100px;">
                        </a>
                    </div>
                    <div class="login-main mb-lg-5 mb-4">
                        <div class="theme-form">
                            @csrf
                            <h4>Sign in to account</h4>
                            <p>Enter your NRIC number & password to login</p>

                            <ul class="nav nav-pills nav-fill nav-info gap-2 mt-4 mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item rounded bg-light mb-md-0 mb-2">
                                    <a class="nav-link {{ old('selected_role') == 'parent' || empty(old('selected_role')) == 'parent' ? 'active' : '' }}"
                                        id="parent-tab" data-bs-toggle="pill" href="#tab-parent" role="tab" aria-selected="true">
                                        Login As Parent
                                    </a>
                                </li>
                                <li class="nav-item rounded bg-light">
                                    <a class="nav-link {{ old('selected_role') == 'staff' ? 'active' : '' }}" id="admin-tab"
                                        data-bs-toggle="pill" href="#tab-admin" role="tab" aria-selected="false">
                                        Login As Staff
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="login-tabContent">
                                <div class="tab-pane fade {{ old('selected_role') == 'parent' || empty(old('selected_role')) == 'parent' ? 'show active' : '' }}"
                                    id="tab-parent" role="tabpanel" aria-labelledby="tab-parent-tab">
                                    <input type="hidden" name="role" value="parent">
                                </div>
                                <div class="tab-pane fade {{ old('selected_role') == 'staff' ? 'show active' : '' }}" id="tab-admin" role="tabpanel"
                                    aria-labelledby="tab-admin-tab">
                                    <input type="hidden" name="role" value="staff">
                                </div>
                            </div>

                            <form action="{{ route('user.login') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="col-form-label">NRIC Number</label>
                                    <input class="form-control" type="text" name="nric_number" placeholder="123456121234" maxlength="12"
                                        oninput="this.value=this.value.replace(/[^0-9.]/g, '')" value="{{ old('nric_number') }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Password</label>
                                    <div class="form-input position-relative">
                                        <input class="form-control" type="password" name="password" placeholder="************" required>
                                        <div class="show-hide"><span class="show"></span></div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="checkbox p-0">
                                        <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') == 1 ? 'checked' : '' }}>
                                        <label class="text-muted" for="remember">Remember me</label>
                                    </div>
                                    <a class="link" href="{{ route('user.forgot') }}">Forgot password?</a>
                                    <div class="text-end mt-3 mb-2">
                                        <input type="hidden" name="selected_role" value="{{ old('selected_role', 'parent') }}" required>
                                        <button class="btn btn-primary btn-block w-100 py-2" type="submit">Sign in</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // change value on tab click
        $('.nav-pills .nav-link').click(function(e) {
            e.preventDefault();
            const tab = '#' + $(this).prop('href').split('#')[1];
            const input = $(tab).find('input').val();

            $('input[name="selected_role"]').val(input);
        });
    </script>
@endsection
