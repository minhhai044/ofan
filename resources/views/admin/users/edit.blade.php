@extends('admin.layouts.master')
@section('style')
    <link href="{{ asset('theme/admin/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Chỉnh sửa người dùng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Danh sách người dùng</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="needs-validation"
        novalidate>
        @csrf
        @method('PUT')

        <div class="row">
            {{-- ===== COL 8 ===== --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Họ tên --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" id="key_name" name="name"
                                    class="form-control @error('name') is-invalid @enderror" placeholder="VD: Nguyễn Văn A"
                                    value="{{ old('name', $user->name) }}" required minlength="3" maxlength="255">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng nhập họ tên (≥ 3 ký tự).</div>
                                @enderror
                            </div>

                            {{-- Điện thoại (đúng 10 chữ số) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" id="phone" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror" placeholder="VD: 0987654321"
                                    value="{{ old('phone', $user->phone) }}" required pattern="^\d{10}$" maxlength="10"
                                    title="Số điện thoại phải gồm đúng 10 chữ số.">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Số điện thoại phải gồm đúng 10 chữ số.</div>
                                @enderror
                            </div>

                            {{-- Mật khẩu (không bắt buộc khi sửa) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mật khẩu</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Để trống nếu không đổi" minlength="6">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Mật khẩu tối thiểu 6 ký tự.</div>
                                @enderror
                            </div>

                            {{-- Nhập lại mật khẩu (lỗi confirmed nằm ở key password) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nhập lại mật khẩu</label>
                                <input type="password" name="password_confirmation"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Nhập lại mật khẩu" minlength="6">
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng nhập lại mật khẩu khớp với mật khẩu.</div>
                                @endif
                            </div>

                            {{-- Chi nhánh --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Thuộc chi nhánh</label>
                                <select name="branch_id" id="branch_id"
                                    class="form-select select2 @error('branch_id') is-invalid @enderror" >
                                    <option value="">Không thuộc chi nhánh</option>
                                    @foreach ($branches ?? [] as $b)
                                        <option value="{{ $b->id }}" @selected(old('branch_id', $user->branch_id) == $b->id)>
                                            {{ $b->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Giá trị không hợp lệ.</div>
                                @enderror
                            </div>

                            {{-- Mã MISA (TEN_VIETLIEN_SDT) --}}
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-end">
                                    <label class="form-label fw-bold mb-0">Mã MISA <span
                                            class="text-danger">*</span></label>

                                </div>
                                <input type="text" id="code_misa" name="code_misa"
                                    class="form-control mt-2 @error('code_misa') is-invalid @enderror"
                                    placeholder="VD: TRANMINHHAI_0338997846"
                                    value="{{ old('code_misa', $user->code_misa) }}" pattern="^[A-Z0-9_]{2,255}$"
                                    maxlength="30" required title="Chỉ chữ IN HOA, số và dấu gạch dưới (_), 2–255 ký tự">
                                @error('code_misa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Chỉ chữ IN HOA, số và dấu gạch dưới (_), 2–255 ký tự.</div>
                                @enderror
                                <div class="form-text">Bỏ dấu + viết liền (IN HOA) + “_” + SĐT.</div>
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Địa chỉ</label>
                                <input type="text" name="address"
                                    class="form-control @error('address') is-invalid @enderror"
                                    placeholder="VD: 123 Cầu Giấy, Hà Nội" value="{{ old('address', $user->address) }}"
                                    maxlength="255">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Địa chỉ không hợp lệ.</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="VD: a@gmail.com" value="{{ old('email', $user->email) }}"
                                    maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Định dạng email không hợp lệ.</div>
                                @enderror
                            </div>

                            {{-- Ngày sinh --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" id="birthday" name="birthday"
                                    class="form-control @error('birthday') is-invalid @enderror"
                                    value="{{ old('birthday', $user->birthday) }}">
                                @error('birthday')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Ngày sinh không hợp lệ.</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex gap-2 px-3 pb-3">
                        <a href="{{ route('users.index') }}" class="btn btn-light w-50">Hủy</a>
                        <button type="submit" class="btn btn-primary w-50">Cập nhật</button>
                    </div>
                </div>
            </div>

            {{-- ===== COL 4 ===== --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Số/Thông tin ngân hàng --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Số / Thông tin ngân hàng</label>
                                <input type="text" name="bank_info"
                                    class="form-control @error('bank_info') is-invalid @enderror"
                                    placeholder="VD: Vietcombank - 0123456789 - NGUYEN VAN A"
                                    value="{{ old('bank_info', $user->bank_info) }}">
                                @error('bank_info')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Thông tin ngân hàng không hợp lệ.</div>
                                @enderror
                            </div>

                            {{-- Lương cơ bản --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Lương cơ bản</label>
                                <input type="text" id="basic_salary" name="basic_salary"
                                    class="form-control @error('basic_salary') is-invalid @enderror"
                                    placeholder="VD: 8.000.000" value="{{ old('basic_salary', $user->basic_salary) }}">
                                @error('basic_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Lương cơ bản không hợp lệ.</div>
                                @enderror
                            </div>

                            {{-- Ảnh QR ngân hàng --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Ảnh QR ngân hàng</label>
                                <input type="file" id="bank_qr" name="bank_qr" accept="image/*"
                                    class="form-control @error('bank_qr') is-invalid @enderror">
                                @error('bank_qr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng chọn ảnh hợp lệ (jpg, jpeg, png, webp; ≤ 4MB).</div>
                                @enderror
                                <div class="mt-2">

                                    <img src="{{ getImageStorage($user->bank_qr) }}" alt="Bank QR hiện tại"
                                        style="max-height:140px; border:1px solid #eee; padding:3px; border-radius:6px;">
                                    <img id="bankQrPreview" src="#" alt=""
                                        style="max-height:140px; display:none; border:1px solid #eee; padding:3px; border-radius:6px;">
                                </div>
                            </div>

                            {{-- Avatar --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Avatar</label>
                                <input type="file" id="avatar" name="avatar" accept="image/*"
                                    class="form-control @error('avatar') is-invalid @enderror">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng chọn ảnh hợp lệ (jpg, jpeg, png, webp; ≤ 4MB).</div>
                                @enderror
                                <div class="mt-2">
                                    <img src="{{ getImageStorage($user->avatar) }}" alt="Avatar hiện tại"
                                        style="max-height:140px; border:1px solid #eee; padding:3px; border-radius:6px;">

                                         <img id="avatarPreview" src="#" alt=""
                                     style="max-height:140px; display:none; border:1px solid #eee; padding:3px; border-radius:6px;">
                                </div>
                            </div>

                            {{-- Giới tính --}}
                            <div class="col-12">
                                <label class="form-label fw-bold d-block">Giới tính</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_male"
                                            value="0" {{ old('gender', $user->gender) == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gender_male">Nam</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_female"
                                            value="1" {{ old('gender', $user->gender) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gender_female">Nữ</label>
                                    </div>
                                </div>
                                @error('gender')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div> {{-- end card --}}
            </div> {{-- end col-4 --}}
        </div> {{-- row --}}
    </form>
@endsection

@section('script')
    <script src="{{ asset('theme/admin/assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/js/pages/validation.init.js') }}"></script>

    <script>
        $(function() {
            // ===== Select2 =====
            // if ($.fn.select2) {
            //     $('#branch_id').select2({
            //         placeholder: $('#branch_id').data('placeholder') || 'Không thuộc chi nhánh',
            //         allowClear: true,
            //         width: '100%'
            //     });
            // }

            // ===== Preview ảnh khi chọn mới =====
            function previewImg(input, target) {
                const f = input.files && input.files[0];
                if (!f) {
                    $(target).hide();
                    return;
                }
                const reader = new FileReader();
                reader.onload = e => {
                    $(target).attr('src', e.target.result).show();
                };
                reader.readAsDataURL(f);
            }
            $('#avatar').on('change', function() {
                previewImg(this, '#avatarPreview');
            });
            $('#bank_qr').on('change', function() {
                previewImg(this, '#bankQrPreview');
            });

            // ===== Auto-generate code_misa: TEN_VIETLIEN + "_" + SDT =====
            let codeTouched = ($('#code_misa').val() || '').trim().length > 0; // có sẵn -> không ghi đè
            $('#code_misa').on('input', function() {
                codeTouched = true;
            });

            function toCode(str) {
                return (str || '')
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // bỏ dấu
                    .replace(/[^a-zA-Z0-9\s]/g, '') // bỏ ký tự đặc biệt
                    .replace(/\s+/g, '') // bỏ khoảng trắng
                    .toUpperCase();
            }

            function recomputeMisa() {
                const name = toCode($('#key_name').val());
                const phone = ($('#phone').val() || '').replace(/\D/g, '');
                let code = name;
                if (phone) code += '_' + phone;
                if (code.length > 30) code = code.slice(0, 30);
                $('#code_misa').val(code);
            }
            // Nút "Tạo lại"
            // $('#regen_code_misa').on('click', function() {
            //     codeTouched = false; // cho phép ghi đè
            //     recomputeMisa();
            // });
            // Nếu người dùng xoá trống code -> cho phép tự sinh lại khi gõ name/phone
            $('#code_misa').on('blur', function() {
                if (!$(this).val().trim()) {
                    codeTouched = false;
                    recomputeMisa();
                }
            });
            // Nếu code chưa bị người dùng chỉnh sau khi xoá trống, cho phép đồng bộ khi đổi name/phone
            $('#key_name, #phone').on('input', function() {
                if (!codeTouched) recomputeMisa();
            });

            // ===== Lương cơ bản: chèn dấu chấm khi nhập, gửi số sạch =====
            const fmtVN = new Intl.NumberFormat('vi-VN');
            (function initSalary() {
                const raw = ($('#basic_salary').val() || '').toString().replace(/\D/g, '');
                $('#basic_salary').val(raw ? fmtVN.format(raw) : '');
            })();
            $(document).on('input', '#basic_salary', function() {
                const raw = this.value.replace(/\D/g, '');
                this.value = raw ? fmtVN.format(raw) : '';
            });

            // ===== Chuẩn Register: chặn submit nếu invalid + ép IN HOA code =====
            $('form.needs-validation').on('submit', function(e) {
                // ép IN HOA code_misa và bỏ dấu chấm lương
                $('#code_misa').val(($('#code_misa').val() || '').trim().toUpperCase());
                $('#basic_salary').val(($('#basic_salary').val() || '').replace(/\D/g, ''));

                if (this.checkValidity() === false) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    $(this).find(':invalid').first().focus();
                    return;
                }
            });
        });
    </script>
@endsection
