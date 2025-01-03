@extends('layouts.app')

@section('page', 'Forgot Password')

@section('content')
    <div class="row m-0">
        <div class="col-12 p-0">
            <div class="login-card login-dark" 
                style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('{{ asset('itrack/images/covers/bg-login.jpg') }}');">
                <div>
                    <div>
                        <a class="logo" href="{{ route('user.index') }}">
                            <img class="img-fluid for-light" src="{{ asset('itrack/images/logo-2.png') }}" alt="logo" style="height: 40px; max-width: 100px;">
                        </a>
                    </div>
                    <div class="login-main mb-lg-5 mb-4">
                        <div class="theme-form">
                            @csrf
                            <h4>Recover Your Account</h4>
                            <p class="mb-3">Enter your NRIC number or Email Address to receive new password</p>

                            <form action="{{ route('user.reset') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="col-form-label">NRIC Number or Email Address</label>
                                    <input class="form-control" type="text" name="username" value="{{ old('username') }}" required>
                                </div>
                                <div class="mt-4 mb-3">
                                    <button class="btn btn-primary btn-block w-100 py-2" type="submit">Confirm</button>
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('user.index') }}" class="mb-0">Back to Login page</a>
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
    <script></script>
@endsection