@extends('layouts.app')

<!-- Main Content -->
@section('content')
    <style>
        html,
        body {
            height: 100%;
        }

        .login-container {
            min-height: 100vh;
        }

        .login-form {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 1rem;
            padding: 2rem;
        }

        .bg-image {
            background-image: url("{{ asset('images/crm_login.png') }}");
            background-size: cover;
            background-position: center;
        }

        .form-group .control-label {
            font-size: 20px;
            font-weight: 600
        }
       .form-label{
        font-size: 16px;
        font-weight: 600
       }
        .Send-Password {
            transition: all 0.6s ease;
            background-color: #3a485c;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            /* font-family: 'Magra', sans-serif; */
            color: #fff;
            text-align: center;
            letter-spacing: 1px;
            width: 100%;
            padding: 14px 0px !important;
            font-size: 14px !important;
            font-weight: 700;
            box-shadow: 0px 3px 9px 1px rgba(0, 0, 0, 0.25);
            margin-top: 1em;
        }

        .Send-Password:hover {
            background-color: #3a485c;
            color: #fff
        }

        .Send-Password:focus {
            box-shadow: none
        }

        .submit-w3 {
            font-size: 16px !important;
        }

        .submit-w3:hover {
            color: #fff
        }

        .panel {
            max-width: 400px;
            margin: 0 auto;
            border: none;
            border-radius: 8px;
            background-color: #fff;
        }

        .panel-heading {
            font-size: 1.25rem;
            color: #333;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 5px;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            font-size: 1rem;
            color: #495057;
        }

        .form-control:focus {
            box-shadow: none;
            outline: none;
            border: 1px solid #ced4da;
        }

        .btn {
            border-radius: 5px;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        input{
            background-color: light-dark(rgb(232, 240, 254), rgba(70, 90, 126, 0.4)) !important;
            color: fieldtext !important;
        }

        input:focus {
            appearance: menulist-button;
            background-image: none !important;
            background-color: light-dark(rgb(232, 240, 254), rgba(70, 90, 126, 0.4)) !important;
            color: fieldtext !important;
        }

        /* .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    } */

        /* .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
        transition: background-color 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    } */


        @media (max-width: 767.98px) {
            .bg-image {
                min-height: 50vh;
            }
        }
    </style>
    <div class="login-container bg-white">
        <div class="row g-0 vh-100">
            <div class="col-md-8 bg-image d-none d-md-block bg-dark">

            </div>
            <div class="col-md-4 d-flex justify-content-center align-items-center px-5">
                {{-- <div class="panel panel-default">
                    <h3 style="text-align:center;">
                        <img src="images/logo.jpg">
                    </h3>
                    <div class="panel-heading">Reset Password</div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form class="form-horizontal w-100 px-2" role="form" method="POST" action="{{ url('/password/email') }}">
                            {!! csrf_field() !!}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="control-label">E-Mail Address</label>

                                <div class="col-md-12">
                                    <input type="email" class="input-field" name="email" value="{{ old('email') }}">

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                <div class="col-md-7">
                                    <button type="submit" class="btn Send-Password mt-0">
                                        <i class="fa fa-btn fa-envelope"></i>Send Password Reset Link
                                    </button>
                                </div>
                                <div class="col-5">
                                <a href="/login.blade.php">
                                <input class="submit-w3 mt-0" type="" value="Login"></a></div>
                            </div>
                        </div>
                        </form>

                    </div>
                </div> --}}
                <div class="panel panel-default p-4 w-100">
                    <h3 class="text-center mb-4">
                        <img src="{{ asset('images/logo.jpg') }}" alt="">
                        {{-- <img src="images/logo.jpg" alt="Logo" class="img-fluid" style="max-width: 100px;"> --}}
                    </h3>
                    <div class="panel-heading text-center h5 mb-4 fw-bold">Reset your password</div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                            {!! csrf_field() !!}
                            {{-- <input type="hidden" name="_token" value="JUBBaUWaeuUI0x7oO9OajUuFsXrzhwbYXdsp68Qs"> --}}

                            <!-- Email Field -->
                            <div class="form-group mb-3 {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="form-label">E-Mail Address</label>
                                <input type="email" class="form-control mb-2" name="email" id="email"
                                    placeholder="Enter your email" required value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <!-- Buttons -->
                            <div class="form-group d-flex justify-content-between gap-2 mt-3">
                                <button type="submit" class="btn Send-Password">
                                    <i class="fa fa-envelope me-2"></i>Send Password Reset Link
                                </button>
                                <a href="{{ url('/login') }}"
                                    class="btn submit-w3 w-auto d-flex align-items-center">Login</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
