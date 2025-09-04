<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Register | Skote - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href=" {{ asset('theme/admin/assets/css/bootstrap.min.css') }} " id="bootstrap-style" rel="stylesheet"
        type="text/css" />
    <!-- Icons Css -->
    <link href=" {{ asset('theme/admin/assets/css/icons.min.css') }} " rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href=" {{ asset('theme/admin/assets/css/app.min.css') }} " id="app-style" rel="stylesheet" type="text/css" />
    <!-- App js -->
    {{-- <script src="assets/js/plugin.js"></script> --}}

</head>

<body>

    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary-subtle">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Đăng ký tài khoản</h5>

                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src=" {{ asset('theme/admin/assets/images/profile-img.png') }} " alt=""
                                        class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div>
                                <a href="index.html">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src=" {{ asset('theme/admin/assets/images/logo.svg') }} "
                                                alt="" class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2">
                                <form class="needs-validation" novalidate action="{{ route('register') }}"
                                    method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Họ và tên</label>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                                            @class(['form-control', 'is-invalid' => $errors->has('name')]) placeholder="Nhập họ và tên" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Vui lòng nhập họ và tên</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                            @class(['form-control', 'is-invalid' => $errors->has('phone')]) placeholder="Số điện thoại" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Vui lòng nhập số điện thoại</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mật khẩu</label>
                                        <input type="password" id="password" name="password"
                                            @class(['form-control', 'is-invalid' => $errors->has('password')]) placeholder="Nhập mật khẩu" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            @class(['form-control', 'is-invalid' => $errors->has('password')]) placeholder="Nhập lại mật khẩu" required>
                                        {{-- Lưu ý: lỗi "confirmed" sẽ nằm ở key 'password' --}}
                                        @if ($errors->has('password'))
                                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                        @else
                                            <div class="invalid-feedback">Vui lòng nhập lại mật khẩu</div>
                                        @endif
                                    </div>
                                    <div class="mt-4 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Đăng
                                            ký</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="{{ route('form_login') }}">
                                            <h5 class="font-size-14 mb-3">Đăn nhập ngay</h5>
                                        </a>

                                        {{-- <ul class="list-inline">
                                            <li class="list-inline-item">
                                                <a href="javascript::void()"
                                                    class="social-list-item bg-primary text-white border-primary">
                                                    <i class="mdi mdi-facebook"></i>
                                                </a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript::void()"
                                                    class="social-list-item bg-info text-white border-info">
                                                    <i class="mdi mdi-twitter"></i>
                                                </a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript::void()"
                                                    class="social-list-item bg-danger text-white border-danger">
                                                    <i class="mdi mdi-google"></i>
                                                </a>
                                            </li>
                                        </ul> --}}
                                    </div>

                                    <div class="mt-4 text-center">
                                        <p class="mb-0">By registering you agree to the Skote <a href="#"
                                                class="text-primary">Terms of Use</a></p>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    {{-- <div class="mt-5 text-center">

                        <div>
                            <p>Already have an account ? <a href="auth-login.html" class="fw-medium text-primary">
                                    Login</a> </p>
                            <p>©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> Skote. Crafted with <i class="mdi mdi-heart text-danger"></i>
                                by Themesbrand
                            </p>
                        </div>
                    </div> --}}

                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src=" {{ asset('theme/admin/assets/libs/jquery/jquery.min.js') }} "></script>
    <script src=" {{ asset('theme/admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }} "></script>
    <script src=" {{ asset('theme/admin/assets/libs/metismenu/metisMenu.min.js') }} "></script>
    <script src=" {{ asset('theme/admin/assets/libs/simplebar/simplebar.min.js') }} "></script>
    <script src=" {{ asset('theme/admin/assets/libs/node-waves/waves.min.js') }} "></script>

    <!-- validation init -->
    <script src=" {{ asset('theme/admin/assets/js/pages/validation.init.js') }} "></script>

    <!-- App js -->
    <script src=" {{ asset('theme/admin/assets/js/app.js') }} "></script>

</body>

</html>
