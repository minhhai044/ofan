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
                <h4 class="mb-sm-0 font-size-18">Chỉnh sửa danh mục</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('product_categories.index') }}">Danh sách danh mục</a>
                        </li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('product_categories.update', $product_category) }}" method="POST" enctype="multipart/form-data"
        class="needs-validation" novalidate>
        @csrf
        @method('PUT')

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
                                    value="{{ old('name', $product_category->name) }}" required minlength="2"
                                    maxlength="255">
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
                                    {{-- Ảnh hiện tại --}}
                                    @if ($product_category->image)
                                        <img src="{{ getImageStorage($product_category->image) }}" alt="Ảnh hiện tại"
                                            style="max-height:140px; border:1px solid #eee; padding:3px; border-radius:6px; margin-right:8px;">
                                    @endif
                                    {{-- Preview ảnh mới --}}
                                    <img id="imagePreview" class="thumb-preview" src="#" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 px-3 pb-3">
                        <a href="{{ route('product_categories.index') }}" class="btn btn-light w-50">Hủy</a>
                        <button type="submit" class="btn btn-primary w-50">Cập nhật</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="{{ asset('theme/admin/assets/js/pages/validation.init.js') }}"></script>
    <script>
        $(function() {
            // Preview ảnh mới
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

            // Chặn submit nếu form invalid (chuẩn Register)
            // $('form.needs-validation').on('submit', function(e) {
            //     if (this.checkValidity() === false) {
            //         e.preventDefault();
            //         e.stopPropagation();
            //         $(this).addClass('was-validated');
            //         $(this).find(':invalid').first().focus();
            //     }
            // });
        });
    </script>
@endsection
