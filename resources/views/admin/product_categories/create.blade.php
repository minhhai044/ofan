@extends('admin.layouts.master')
@section('style')
    <style>
        .thumb-preview {
            max-height: 140px;
            border: 1px solid #eee;
            padding: 3px;
            border-radius: 6px;
            display: none;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Thêm mới danh mục</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('product_categories.index') }}">Danh sách danh mục</a>
                        </li>
                        <li class="breadcrumb-item active">Thêm mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('product_categories.store') }}" method="POST" enctype="multipart/form-data"
        class="needs-validation" novalidate>
        @csrf

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Tên danh mục --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror" placeholder="Nhập tên danh mục"
                                    value="{{ old('name') }}" required minlength="2" maxlength="255">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng nhập tên danh mục (≥ 2 ký tự).</div>
                                @enderror
                            </div>




                            {{-- Ảnh logo --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ảnh logo</label>
                                <input type="file" id="image" name="image" accept="image/*"
                                    class="form-control @error('image') is-invalid @enderror">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng chọn ảnh hợp lệ (jpg, jpeg, png, webp; ≤ 4MB).</div>
                                @enderror
                                <div class="mt-2">
                                    <img id="imagePreview" class="thumb-preview" src="#" alt="">
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="d-flex gap-2 px-3 pb-3">
                        <a href="{{ route('product_categories.index') }}" class="btn btn-light w-50">Hủy</a>
                        <button type="submit" class="btn btn-primary w-50">Tạo mới</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src=" {{ asset('theme/admin/assets/js/pages/validation.init.js') }} "></script>

    <script>
        $(function() {
            // ===== Preview ảnh logo =====
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
            $('#image').on('change', function() {
                previewImg(this, '#imagePreview');
            });

            // ===== Tạo slug từ name (xử lý Đ/đ) =====
            // function toSlug(str) {
            //     return (str || '')
            //         .toString()
            //         .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // bỏ dấu
            //         .replace(/[đĐ]/g, 'd') // đ/Đ -> d
            //         .toLowerCase()
            //         .replace(/[^a-z0-9\s-]/g, '') // bỏ ký tự đặc biệt
            //         .trim()
            //         .replace(/\s+/g, '-') // space -> -
            //         .replace(/-+/g, '-') // gộp nhiều - về 1
            //         .replace(/^-|-$/g, ''); // bỏ - ở đầu/cuối
            // }

            // let slugTouched = ($('#slug').val() || '').trim().length > 0;
            // $('#slug').on('input', function() {
            //     slugTouched = true;
            // });

            // function recomputeSlug() {
            //     if (slugTouched) return;
            //     $('#slug').val(toSlug($('#name').val()));
            // }

            // $('#name').on('input', function() {
            //     if (!slugTouched) recomputeSlug();
            // });

            // $('#regen_slug').on('click', function() {
            //     slugTouched = false;
            //     $('#slug').val(toSlug($('#name').val()));
            // });

            // // ===== Chuẩn Register: chặn submit nếu invalid =====
            // $('form.needs-validation').on('submit', function(e) {
            //     // ép lại slug lần cuối nếu để trống
            //     if (!$('#slug').val().trim()) {
            //         $('#slug').val(toSlug($('#name').val()));
            //     }
            //     if (this.checkValidity() === false) {
            //         e.preventDefault();
            //         e.stopPropagation();
            //         $(this).addClass('was-validated');
            //         $(this).find(':invalid').first().focus();
            //         return;
            //     }
            // });
        });
    </script>
@endsection
