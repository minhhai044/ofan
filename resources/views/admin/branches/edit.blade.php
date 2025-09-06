@extends('admin.layouts.master')
@section('style')
    <link href="{{ asset('theme/admin/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Chỉnh sửa chi nhánh</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('branches.index') }}">Danh sách chi nhánh</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('branches.update', $branch) }}" method="POST" enctype="multipart/form-data"
        class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            {{-- Tên chi nhánh --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="key_name" class="form-label fw-bold">
                                        Tên chi nhánh <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="key_name" name="name" placeholder="Nhập tên chi nhánh"
                                        value="{{ old('name', $branch->name) }}" required minlength="3">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">Vui lòng nhập tên chi nhánh (tối thiểu 3 ký tự)</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="key_address" class="form-label fw-bold">
                                        Địa chỉ <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                        id="key_address" name="address" placeholder="Nhập địa chỉ"
                                        value="{{ old('address', $branch->address) }}" required minlength="5">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">Vui lòng nhập địa chỉ (tối thiểu 5 ký tự)</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Chi nhánh cha (không bắt buộc) --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="branch_id" class="form-label fw-bold">Chọn chi nhánh cha</label>
                                    <select name="branch_id"
                                        class="form-select select2 @error('branch_id') is-invalid @enderror" id="branch_id"
                                        data-placeholder="Không có nhánh cha">
                                        <option value="">Không có nhánh cha</option>
                                        @foreach ($branches as $b)
                                            @if ($b->id !== $branch->id)
                                                {{-- Không cho chọn chính nó làm cha --}}
                                                <option value="{{ $b->id }}" @selected(old('branch_id', $branch->branch_id) == $b->id)>
                                                    {{ $b->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">Giá trị không hợp lệ.</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label fw-bold">Chọn vai trò</label>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror select2"
                                        id="type">
                                        <option @selected($branch->type == 2) value="2">Cộng tác viên</option>
                                        <option @selected($branch->type == 1) value="1">Đại lý</option>
                                        <option @selected($branch->type == 0) value="0">Nhà phân phối</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">Giá trị không hợp lệ.</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Mã MISA (tự sinh từ tên; cho phép trống) --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code_misa" class="form-label fw-bold">Mã MISA</label>
                                    <input type="text" class="form-control @error('code_misa') is-invalid @enderror"
                                        id="code_misa" name="code_misa" placeholder="Nhập mã MISA (nếu muốn)"
                                        value="{{ old('code_misa', $branch->code_misa) }}" pattern="[A-Z0-9]{2,255}"
                                        title="Chỉ chữ IN HOA và số, không dấu/khoảng trắng, 2–30 ký tự">
                                    @error('code_misa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">
                                            Mã MISA chỉ gồm chữ IN HOA và số (2–255 ký tự), không khoảng trắng.
                                        </div>
                                    @enderror
                                    <div class="form-text">Tự động sinh từ tên: bỏ dấu + viết liền + IN HOA.</div>
                                </div>
                            </div>

                            <div class="col-12 d-flex gap-2">
                                <a href="{{ route('branches.index') }}" class="btn btn-light">Hủy</a>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </div>
                        </div> {{-- row --}}

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="{{ asset('theme/admin/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/js/pages/validation.init.js') }}"></script>

    <script>
        $(function() {
            var codeTouched = false;
            $('#code_misa').on('input', function() {
                codeTouched = true;
            });

            const toCode = s => (s || '')
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[đĐ]/g, 'd') 
                .replace(/[^0-9A-Za-z]+/g, '')
                .toUpperCase();


            if (!$('#code_misa').val()) {
                var initCode = toCode($('#key_name').val());
                $('#code_misa').val(initCode);
            }

            $('#key_name').on('input', function() {
                if (codeTouched) return;
                $('#code_misa').val(toCode($(this).val()));
            });


        });
    </script>
@endsection
