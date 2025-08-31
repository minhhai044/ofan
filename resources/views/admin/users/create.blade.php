@extends('admin.layouts.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Thêm mới người dùng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Danh sách người dùng</a></li>
                        <li class="breadcrumb-item active">Thêm mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation"
        novalidate>
        @csrf

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
                                    value="{{ old('name') }}" required minlength="3" maxlength="255">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng nhập họ tên (≥ 3 ký tự).</div>
                                @enderror
                            </div>

                            {{-- Điện thoại --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" id="phone" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror" placeholder="VD: 0987654321"
                                    value="{{ old('phone') }}" required pattern="[0-9+\-\s]{8,20}" maxlength="20">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">SĐT 8–20 ký tự, chỉ gồm số/khoảng trắng/+/-.</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" placeholder="VD: a@gmail.com"
                                    value="{{ old('email') }}" maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Định dạng email không hợp lệ.</div>
                                @enderror
                            </div>

                            {{-- Chi nhánh --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Thuộc chi nhánh</label>
                                <select name="branch_id" id="branch_id"
                                    class="form-select @error('branch_id') is-invalid @enderror"
                                    data-placeholder="— Không thuộc chi nhánh —">
                                    <option value="">— Không thuộc chi nhánh —</option>
                                    @foreach ($branches ?? [] as $b)
                                        <option value="{{ $b->id }}" @selected(old('branch_id') == $b->id)>{{ $b->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Giá trị không hợp lệ.</div>
                                @enderror
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Địa chỉ</label>
                                <input type="text" name="address"
                                    class="form-control @error('address') is-invalid @enderror"
                                    placeholder="VD: 123 Cầu Giấy, Hà Nội" value="{{ old('address') }}" maxlength="255">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Địa chỉ không hợp lệ.</div>
                                @enderror
                            </div>

                            {{-- Mã MISA --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mã MISA <span class="text-danger">*</span></label>
                                <input type="text" id="code_misa" name="code_misa"
                                    class="form-control @error('code_misa') is-invalid @enderror"
                                    placeholder="Tự sinh từ Họ tên + SĐT (bỏ dấu, viết liền, IN HOA)"
                                    value="{{ old('code_misa') }}" pattern="[A-Z0-9]{2,30}" maxlength="30" required
                                    title="Chỉ chữ IN HOA và số, không khoảng trắng, 2–30 ký tự">
                                @error('code_misa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Chỉ chữ IN HOA & số (2–30 ký tự), không khoảng trắng.</div>
                                @enderror
                                <div class="form-text">Ví dụ: NGUYENVANA0987654321</div>
                            </div>

                            {{-- Lương cơ bản --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Lương cơ bản</label>
                                <input type="number" name="basic_salary"
                                    class="form-control @error('basic_salary') is-invalid @enderror"
                                    placeholder="VD: 8000000" value="{{ old('basic_salary', 0) }}" min="0"
                                    step="1">
                                @error('basic_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Ngày sinh --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" id="birthday" name="birthday"
                                    class="form-control @error('birthday') is-invalid @enderror"
                                    value="{{ old('birthday') }}">
                                @error('birthday')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="••••••"
                                    required minlength="6">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng nhập mật khẩu (≥ 6 ký tự).</div>
                                @enderror
                            </div>
                        </div>
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
                                    value="{{ old('bank_info') }}">
                                @error('bank_info')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Ảnh ngân hàng (QR) --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Ảnh QR ngân hàng</label>
                                <input type="file" id="bank_qr" name="bank_qr" accept="image/*"
                                    class="form-control @error('bank_qr') is-invalid @enderror">
                                @error('bank_qr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    <img id="bankQrPreview" src="#" alt=""
                                        style="max-height:140px; display:none; border:1px solid #eee; padding:3px; border-radius:6px;">
                                </div>
                            </div>

                            {{-- Ảnh đại diện --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Avatar</label>
                                <input type="file" id="avatar" name="avatar" accept="image/*"
                                    class="form-control @error('avatar') is-invalid @enderror">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
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
                                            value="0" {{ old('gender', 0) == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gender_male">Nam</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="gender_female"
                                            value="1" {{ old('gender', 0) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gender_female">Nữ</label>
                                    </div>
                                </div>
                                @error('gender')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nút hành động --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-light w-50">Hủy</a>
                    <button type="submit" class="btn btn-primary w-50">Tạo mới</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $(function() {
            // ===== Select2 (nếu dự án đã include plugin) =====
            if ($.fn.select2) {
                $('#branch_id').select2({
                    placeholder: $('#branch_id').data('placeholder') || '— Không thuộc chi nhánh —',
                    allowClear: true,
                    width: '100%'
                });
            }

            // ===== Preview ảnh =====
            function previewImg(input, target) {
                const file = input.files && input.files[0];
                if (!file) {
                    $(target).hide();
                    return;
                }
                const reader = new FileReader();
                reader.onload = e => {
                    $(target).attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
            $('#avatar').on('change', function() {
                previewImg(this, '#avatarPreview');
            });
            $('#bank_qr').on('change', function() {
                previewImg(this, '#bankQrPreview');
            });

            // ===== Auto-generate code_misa từ name + phone (viết liền + IN HOA) =====
            let codeTouched = false;
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
                if (codeTouched) return; // user đã sửa tay -> không ghi đè
                const name = toCode($('#key_name').val());
                const phone = ($('#phone').val() || '').replace(/\D/g, ''); // chỉ lấy số
                let code = name;
                if (phone) code += '_' + phone; // chèn gạch dưới khi có SĐT
                if (code.length > 30) code = code.slice(0, 30); // cắt theo pattern
                $('#code_misa').val(code);
            }

            $('#key_name, #phone').on('input', recomputeMisa);
            if (!$('#code_misa').val()) recomputeMisa();

            // Trước khi submit: ép IN HOA + trim (đề phòng user gõ thường)
            $('form.needs-validation').on('submit', function(e) {
                const cm = ($('#code_misa').val() || '').trim().toUpperCase();
                $('#code_misa').val(cm);
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
