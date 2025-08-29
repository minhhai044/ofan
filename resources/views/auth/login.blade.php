<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <title>Login | Skote - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="{{ asset('theme/admin/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('theme/admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('theme/admin/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
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
                                        <h5 class="text-primary">Chào mừng trở lại!</h5>
                                        <p>Đăng nhập để tiếp tục.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('theme/admin/assets/images/profile-img.png') }}" alt=""
                                        class="img-fluid">
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <div class="auth-logo text-center">
                                <a href="#" class="d-inline-block">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('theme/admin/assets/images/logo.svg') }}" alt=""
                                                class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="p-2">
                                {{-- Thông báo chung --}}
                                {{-- @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (session('status'))
                                    <div class="alert alert-info">{{ session('status') }}</div>
                                @endif --}}

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <div class="fw-semibold mb-1">Đăng nhập không thành công. Vui lòng kiểm tra lại.
                                        </div>
                                        {{-- Có thể liệt kê chi tiết nếu muốn:
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                    --}}
                                    </div>
                                @endif

                                <form class="form-horizontal" method="POST" action="{{ route('login') }}" novalidate>
                                    @csrf

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                            @class(['form-control', 'is-invalid' => $errors->has('phone')]) placeholder="Nhập số điện thoại" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Vui lòng nhập số điện thoại</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mật khẩu</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Nhập mật khẩu" aria-label="Password" required>
                                            <button class="btn btn-light" type="button" data-toggle="password"
                                                data-target="#password">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @else
                                                <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="remember-check"
                                            name="remember" {{ old('remember') ? 'checked' : '' }} value="1">
                                        <label class="form-check-label" for="remember-check">
                                            Ghi nhớ đăng nhập
                                        </label>
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Đăng
                                            nhập</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="{{ route('form_register') }}">
                                            <h5 class="font-size-14 mb-3">Đăng ký ngay</h5>
                                        </a>
                                    </div>

                                    <div class="mt-2 text-center">
                                        <a href="" class="text-muted">
                                            <i class="mdi mdi-lock me-1"></i> Quên mật khẩu?
                                        </a>
                                    </div>
                                </form>
                            </div> <!-- p-2 -->
                        </div> <!-- card-body -->
                    </div> <!-- card -->
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('theme/admin/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/js/app.js') }}"></script>

    <script>
        // Toggle hiện/ẩn mật khẩu
        $(function() {
            $(document).on('click', '[data-toggle="password"]', function(e) {
                e.preventDefault();

                var $btn = $(this);
                var target = $btn.attr('data-target');
                if (!target) return;

                var $input = $(target);
                if ($input.length === 0) return;

                // Chuyển type
                var isHidden = $input.attr('type') === 'password';
                $input.attr('type', isHidden ? 'text' : 'password');

                // Đảo icon (nếu có)
                var $icon = $btn.find('i');
                if ($icon.length) {
                    $icon.toggleClass('mdi-eye-outline mdi-eye-off-outline');
                }
            });
        });
    </script>
</body>

</html>
