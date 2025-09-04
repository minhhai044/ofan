{{-- resources/views/admin/products/edit.blade.php --}}
@extends('admin.layouts.master')

@section('style')
    <link href="{{ asset('theme/admin/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .thumb {
            width: 88px;
            height: 88px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .thumb-wrap {
            position: relative;
            display: inline-block;
            margin: 4px;
        }

        .thumb-wrap .btn-remove {
            position: absolute;
            top: -8px;
            right: -8px;
        }

        .card-section-title {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: .75rem;
        }

        .table thead th {
            vertical-align: middle;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6 !important;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .w-120 {
            width: 120px;
        }

        .w-100px {
            width: 100px;
        }

        .nowrap {
            white-space: nowrap;
        }

        .btn-action {
            padding: .25rem .5rem;
        }

        .repeater-note {
            font-size: 12px;
            color: #6c757d;
        }

        .g-thumb {
            position: relative;
            display: inline-block;
            margin: 6px;
        }

        .g-thumb .btn-remove {
            position: absolute;
            top: -8px;
            right: -8px;
        }

        .existing-image {
            position: relative;
            display: inline-block;
            margin: 4px;
        }

        .existing-image .btn-remove-existing {
            position: absolute;
            top: -8px;
            right: -8px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Chỉnh sửa sản phẩm</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Danh sách sản phẩm</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data"
        class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <div class="row">
            {{-- ===== COL 8: Thông tin, Lõi lọc, Phụ kiện ===== --}}
            <div class="col-lg-8">

                {{-- Thông tin cơ bản --}}
                <div class="card mb-3">
                    <div class="card-body">

                        <div class="row g-3">

                            {{-- Danh mục --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                <select name="product_category_id" id="product_category_id"
                                    class="form-select select2 @error('product_category_id') is-invalid @enderror"
                                    data-placeholder="Chọn danh mục" required>
                                    @foreach ($categories ?? [] as $c)
                                        <option value="{{ $c->id }}" @selected(old('product_category_id', $product->product_category_id) == $c->id)>{{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng chọn danh mục.</div>
                                @enderror
                            </div>

                            {{-- Tên --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror" placeholder="VD: Máy lọc nước"
                                    value="{{ old('name', $product->name) }}" required minlength="2" maxlength="255">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng nhập tên (≥ 2 ký tự).</div>
                                @enderror
                            </div>

                            {{-- SKU --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">SKU <span class="text-danger">*</span></label>
                                <input type="text" id="sku" name="sku"
                                    class="form-control @error('sku') is-invalid @enderror" placeholder="VD: ALX-1000"
                                    value="{{ old('sku', $product->sku) }}" required maxlength="100">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng nhập SKU.</div>
                                @enderror
                            </div>

                            {{-- code_misa + nút tái tạo --}}
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-end">
                                    <label class="form-label fw-bold mb-0">Mã MISA <span
                                            class="text-danger">*</span></label>
                                </div>
                                <input type="text" id="code_misa" name="code_misa"
                                    class="form-control mt-2 @error('code_misa') is-invalid @enderror"
                                    placeholder="VD: MAYLOCNUOC" value="{{ old('code_misa', $product->code_misa) }}"
                                    required pattern="^[A-Z0-9_]{2,100}$" maxlength="100">
                                @error('code_misa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">IN HOA, số, gạch dưới (_), 2–100 ký tự.</div>
                                @enderror
                            </div>

                            {{-- Mã vạch --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Mã vạch (Barcode) <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="bar_code" name="bar_code"
                                    class="form-control @error('bar_code') is-invalid @enderror"
                                    placeholder="VD: 8938505970xxx" value="{{ old('bar_code', $product->bar_code) }}"
                                    required maxlength="100">
                                @error('bar_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng nhập mã vạch.</div>
                                @enderror
                            </div>

                            {{-- Giá & Giá KM --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Giá (VNĐ) <span class="text-danger">*</span></label>
                                <input type="text" id="price" name="price"
                                    class="form-control currency-dot @error('price') is-invalid @enderror"
                                    placeholder="VD: 12.500.000"
                                    value="{{ old('price', number_format($product->price, 0, ',', '.')) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Vui lòng nhập giá hợp lệ.</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Giá khuyến mãi (VNĐ)</label>
                                <input type="text" id="price_sale" name="price_sale"
                                    class="form-control currency-dot @error('price_sale') is-invalid @enderror"
                                    placeholder="VD: 10.990.000"
                                    value="{{ old('price_sale', $product->price_sale ? number_format($product->price_sale, 0, ',', '.') : '') }}">
                                @error('price_sale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hoa hồng, Số cấp lọc, Đơn vị --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Hoa hồng (%)</label>
                                <input type="number" step="0.01" min="0" max="100"
                                    id="commission_discount" name="commission_discount"
                                    class="form-control @error('commission_discount') is-invalid @enderror"
                                    placeholder="VD: 5"
                                    value="{{ old('commission_discount', $product->commission_discount) }}">
                                @error('commission_discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số cấp lọc</label>
                                <input type="number" min="0" max="50" id="filter_stages"
                                    name="filter_stages" class="form-control @error('filter_stages') is-invalid @enderror"
                                    placeholder="VD: 7" value="{{ old('filter_stages', $product->filter_stages) }}">
                                @error('filter_stages')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Đơn vị</label>
                                <input type="text" id="unit" name="unit"
                                    class="form-control @error('unit') is-invalid @enderror"
                                    placeholder="VD: bộ / máy / chiếc" value="{{ old('unit', $product->unit) }}"
                                    maxlength="50">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                {{-- Lõi lọc (product_filters) --}}
                                <div class=" mb-3">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="card-section-title m-0">Thêm lõi lọc</div>
                                        <button type="button" class="btn btn-sm btn-primary" id="btnAddFilter">
                                            <i class="bx bx-plus"></i> Thêm lõi lọc
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle mb-0">
                                            <thead>
                                                <tr class="text-center">
                                                    <th style="min-width:280px">Lõi lọc</th>
                                                    <th class="w-120">Ảnh</th>
                                                    <th class="w-120">Tháng</th>
                                                    <th class="w-100px">Số lượng</th>
                                                    <th class="w-60">Trạng thái</th>
                                                    <th class="nowrap" style="width:80px">Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody id="filtersBody">

                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                                {{-- Phụ kiện (product_accessories) --}}
                                <div class=" mb-3">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="card-section-title m-0">Thêm phụ kiện</div>
                                        <button type="button" class="btn btn-sm btn-primary" id="btnAddAccessory">
                                            <i class="bx bx-plus"></i> Thêm phụ kiện
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle mb-0">
                                            <thead>
                                                <tr class="text-center">
                                                    <th style="min-width:280px">Phụ kiện</th>
                                                    <th class="w-100px">Ảnh</th>
                                                    <th class="w-100px">Số lượng</th>
                                                    <th class="w-120">Trạng thái</th>
                                                    <th class="nowrap" style="width:80px">Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody id="accessoriesBody">

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                            {{-- Mô tả --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Mô tả</label>
                                <textarea name="description" id="description" rows="5"
                                    class="form-control ckeditor @error('description') is-invalid @enderror"
                                    placeholder="Mô tả chi tiết sản phẩm...">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== COL 4: Trạng thái & Ảnh ===== --}}
            <div class="col-lg-4">

                {{-- Cài đặt --}}
                <div class="card mb-3">
                    <div class="card-body">

                        <div class="row g-3">
                            <div class="col-12 d-flex align-items-center justify-content-between">
                                <label class="form-label m-0">Kích hoạt</label>
                                <div class="form-check form-switch m-0">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active"
                                        name="is_active" value="1" @checked($product->is_active)>
                                </div>
                            </div>
                            <div class="col-12 d-flex align-items-center justify-content-between">
                                <label class="form-label m-0">Gắn nhãn "Đặc biệt"</label>
                                <div class="form-check form-switch m-0">
                                    <input type="hidden" name="is_special" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_special"
                                        name="is_special" value="1" @checked($product->is_special)>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="card-section-title">Hình ảnh sản phẩm</div>

                        {{-- Ảnh đại diện (1 ảnh) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ảnh đại diện</label>
                            <input type="file" id="featured_image" name="featured_image" accept="image/*"
                                class="form-control @error('featured_image') is-invalid @enderror">
                            @error('featured_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            {{-- Hiển thị ảnh hiện có --}}
                            @if ($product->images)
                                @foreach ($product->images as $images)
                                    @if ($images['status'] == 1)
                                        <div class="mt-2">
                                            <div class="existing-image">
                                                <img src="{{ getImageStorage($images['image']) }}" class="thumb"
                                                    alt="featured">
                                                <button type="button" class="btn btn-sm btn-danger btn-remove-existing"
                                                    data-field="featured_image" data-id="{{ $images['id'] }}">
                                                    <i class="bx bx-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img id="featuredPreview" class="thumb" style="display:none" alt="featured">
                                <button type="button" id="btnClearFeatured" class="btn btn-sm btn-outline-secondary"
                                    style="display:none">
                                    Xóa
                                </button>
                            </div>
                            <div class="form-text">Chỉ chọn 1 ảnh. Nên dùng tỉ lệ 1:1 hoặc 4:3.</div>
                        </div>

                        {{-- Ảnh bộ sưu tập (nhiều ảnh) --}}
                        <div class="mb-2">
                            <label class="form-label fw-bold">Ảnh bộ sưu tập</label>
                            <input type="file" id="gallery_images" name="featured_images[]" multiple accept="image/*"
                                class="form-control @error('featured_images') is-invalid @enderror @error('featured_images.*') is-invalid @enderror">
                            @error('featured_images')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('featured_images.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            {{-- Hiển thị ảnh hiện có --}}
                            @if ($product->images)

                                @if (count($product->images) > 0)
                                    <div class="mt-2" id="existingGallery">
                                        @foreach ($product->images as $index => $images)
                                            @if ($images['status'] == 0)
                                                <div class="existing-image">
                                                    <img src="{{ getImageStorage($images['image']) }}" class="thumb"
                                                        alt="gallery-{{ $index }}">
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-remove-existing"
                                                        data-index="{{ $index }}" data-id="{{ $images['id'] }}">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endif

                            <div class="form-text">Tối đa 10 ảnh, mỗi ảnh ≤ 4MB. Có thể chọn nhiều ảnh.</div>
                        </div>

                        <div id="galleryPreview"></div>

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-light w-50">Hủy</a>
                            <button type="submit" class="btn btn-primary w-50">Cập nhật</button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

    {{-- ===== Templates (JS sẽ clone) ===== --}}
    <script type="text/template" id="tplFilterRow">
        <tr>
            <td>
                <select class="form-select select2" name="filters[__INDEX__][product_filter_id]" data-placeholder="Chọn lõi lọc" >
                    <option value="">-- Chọn lõi lọc --</option>
                    @foreach(($list_filter->products ?? []) as $p)
                        @php
                            // Lấy mảng images (có thể là JSON string hoặc array)
                            $imagesRaw = is_array($p) ? ($p['images'] ?? null) : ($p->images ?? null);
                            $imagesArr = is_string($imagesRaw) ? json_decode($imagesRaw, true) : $imagesRaw;
                            $imagesArr = is_array($imagesArr) ? $imagesArr : [];

                            // Tìm ảnh có status = 1
                            $activeImg = collect($imagesArr)->first(function($it){
                                return (isset($it['status']) ? (int)$it['status'] : 0) == 1;
                            });

                            // Lấy path ảnh và convert sang URL bằng helper
                            $path = is_array($activeImg) ? ($activeImg['image'] ?? null) : null;
                            $thumbUrl = getImageStorage($path);
                        @endphp

                        <option value="{{ is_array($p) ? $p['id'] : $p->id }}"
                                data-thumb="{{ e($thumbUrl) }}">
                            {{ is_array($p) ? $p['name'] : $p->name }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="filters[__INDEX__][id]" value="">
            </td>
            <td class="text-center">
                <span class="text-muted">Chưa chọn</span>
            </td>
            <td class="text-center">
                <input type="number" min="0" class="form-control text-center" name="filters[__INDEX__][maintenance_schedule]" value="6" />
            </td>
            <td class="text-center">
                <input type="number" min="1" class="form-control text-center" name="filters[__INDEX__][quantity]" value="1" />
            </td>
            <td class="text-center">
                <input type="hidden" name="filters[__INDEX__][is_active]" value="0">
                <div class="form-check form-switch d-inline-block">
                    <input class="form-check-input" type="checkbox" value="1" checked
                           name="filters[__INDEX__][is_active]">
                </div>
            </td>
            <td class="text-center nowrap">
                <button type="button" class="btn btn-outline-danger btn-action btnRemoveRow" title="Xóa hàng">
                    <i class="bx bx-x"></i>
                </button>
            </td>
        </tr>
    </script>

    <script type="text/template" id="tplAccessoryRow">
        <tr>
            <td>
                <select class="form-select select2" name="accessories[__INDEX__][product_accessory_id]" data-placeholder="Chọn phụ kiện" >
                    <option value="">-- Chọn phụ kiện --</option>
                    @foreach(($list_accessory->products ?? []) as $p)
                        @php
                            $imagesRaw = is_array($p) ? ($p['images'] ?? null) : ($p->images ?? null);
                            $imagesArr = is_string($imagesRaw) ? json_decode($imagesRaw, true) : $imagesRaw;
                            $imagesArr = is_array($imagesArr) ? $imagesArr : [];

                            $activeImg = collect($imagesArr)->first(function($it){
                                return (isset($it['status']) ? (int)$it['status'] : 0) === 1;
                            });

                            $path = is_array($activeImg) ? ($activeImg['image'] ?? null) : null;
                            $thumbUrl = getImageStorage($path);
                        @endphp

                        <option value="{{ is_array($p) ? $p['id'] : $p->id }}"
                                data-thumb="{{ e($thumbUrl) }}">
                            {{ is_array($p) ? $p['name'] : $p->name }}
                        </option>
                    @endforeach

                </select>
                <input type="hidden" name="accessories[__INDEX__][id]" value="">
            </td>
            <td class="text-center">
                <span class="text-muted">Chưa chọn</span>
            </td>
            <td class="text-center">
                <input type="number" min="1" class="form-control text-center" name="accessories[__INDEX__][quantity]" value="1" />
            </td>
            <td class="text-center">
                <input type="hidden" name="accessories[__INDEX__][is_active]" value="0">
                <div class="form-check form-switch d-inline-block">
                    <input class="form-check-input" type="checkbox" value="1" checked
                           name="accessories[__INDEX__][is_active]">
                </div>
            </td>
            <td class="text-center nowrap">
                <button type="button" class="btn btn-outline-danger btn-action btnRemoveRow" title="Xóa hàng">
                    <i class="bx bx-x"></i>
                </button>
            </td>
        </tr>
    </script>
@endsection

@section('script')
    <script src="{{ asset('theme/admin/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/ckeditor/ckeditor.js') }}"></script>
    <script src=" {{ asset('theme/admin/assets/js/pages/validation.init.js') }} "></script>

    <script>
        $(function() {
            /* ========= Helpers ========= */
            const fmtVN = new Intl.NumberFormat('vi-VN');

            // bỏ dấu, Đ/đ -> d, viết liền, IN HOA
            const toCode = s => (s || '')
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[đĐ]/g, 'd')
                .replace(/[^0-9A-Za-z]+/g, '')
                .toUpperCase();

            function applySelect2(ctx) {
                const $root = ctx ? $(ctx) : $(document);
                $root.find('select.select2').each(function() {
                    const $el = $(this);

                    // nếu đã init trước đó thì destroy để tránh double-init
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                    $el.select2({
                        width: '100%',
                        placeholder: $el.data('placeholder') || 'Chọn...',
                        dropdownParent: $(document.body)
                    });
                });
            }

            /* ========= Auto MISA + Auto Barcode theo Name ========= */
            let misaTouched = false;
            let barcodeTouched = false;
            let barcodeAutoSet = false;

            $('#code_misa').on('input', function() {
                misaTouched = true;
            });
            $('#bar_code').on('input', function() {
                barcodeTouched = true;
                barcodeAutoSet = true;
            });

            $('#name').on('input', function() {
                const name = $(this).val();

                // Tự sinh MISA từ Name (không ghi đè nếu user đã sửa tay)
                if (!misaTouched) {
                    $('#code_misa').val(toCode(name));
                }

            });

            /* ========= Giá: thêm dấu chấm khi nhập ========= */
            const fmt = s => s.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            $('.currency-dot').each(function() {
                this.value = fmt(this.value || '');
            });
            $(document).on('input', '.currency-dot', function() {
                const raw = this.value.replace(/\D/g, '');
                this.value = raw ? fmtVN.format(raw) : '';
            });

            /* ===== Ảnh đại diện (single) ===== */
            (function() {
                const $inp = $('#featured_image');
                const $img = $('#featuredPreview');
                const $btn = $('#btnClearFeatured');

                function clearFeatured() {
                    $inp.val('');
                    $img.hide().attr('src', '');
                    $btn.hide();
                }

                $inp.on('change', function() {
                    const f = this.files && this.files[0];
                    if (!f) {
                        clearFeatured();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = e => {
                        $img.attr('src', e.target.result).show();
                        $btn.show();
                    };
                    reader.readAsDataURL(f);
                });

                $btn.on('click', clearFeatured);
            })();

            /* ===== Ảnh bộ sưu tập (multiple) ===== */
            (function() {
                const $inp = $('#gallery_images');
                const $wrap = $('#galleryPreview');

                const MAX_FILES = 10;
                const MAX_SIZE = 4 * 1024 * 1024;
                const ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

                let files = [];

                function syncInput() {
                    const dt = new DataTransfer();
                    files.forEach(f => dt.items.add(f));
                    $inp[0].files = dt.files;
                }

                function render() {
                    $wrap.empty();
                    files.forEach((f, i) => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            $wrap.append(`
          <div class="g-thumb" data-idx="${i}">
            <button type="button" class="btn btn-sm btn-danger btn-remove"><i class="bx bx-x"></i></button>
            <img class="thumb" src="${e.target.result}" alt="img-${i}">
          </div>
        `);
                        };
                        reader.readAsDataURL(f);
                    });
                }

                $inp.on('change', function() {
                    const selected = Array.from(this.files || []);
                    let valid = [];
                    for (const f of selected) {
                        if (!ALLOWED.includes(f.type)) continue;
                        if (f.size > MAX_SIZE) continue;
                        valid.push(f);
                    }
                    files = files.concat(valid).slice(0, MAX_FILES);
                    syncInput();
                    render();
                });

                $(document).on('click', '.g-thumb .btn-remove', function() {
                    const idx = +$(this).closest('.g-thumb').data('idx');
                    files.splice(idx, 1);
                    syncInput();
                    render();
                });
            })();

            /* ===== Xóa ảnh hiện có ===== */
            $(document).on('click', '.btn-remove-existing', function() {
                const $btn = $(this);
                const $img = $btn.closest('.existing-image');
                const imageId = $btn.data('id');
                const productId = '{{ $product->id }}';

                if (!imageId) {
                    alert('Không tìm thấy ID ảnh!');
                    return;
                }



                // Disable button để tránh click nhiều lần
                $btn.prop('disabled', true);

                // Gửi AJAX request xóa ảnh
                $.ajax({
                    url: '{{ route('products.updateImage', $product->id) }}',
                    method: 'PUT',
                    data: {
                        data: imageId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Xóa ảnh khỏi DOM với hiệu ứng
                        $img.fadeOut(300, function() {
                            $(this).remove();
                        });
                        toastr.success('Xóa ảnh thành công!');
                    },
                    error: function(xhr, status, error) {
                        // Enable lại button khi có lỗi
                        $btn.prop('disabled', false);
                        toastr.error('Xóa ảnh thất bại!');
                    }
                });
            });

            /* ========= Repeater: Filters & Accessories ========= */
            let filterIndex = {{ $product->productFilters ? count($product->productFilters) : 0 }};
            let accessoryIndex = {{ $product->productAccessories ? count($product->productAccessories) : 0 }};

            // Dữ liệu hiện có từ server
            const existingFilters = @json($product->productFilters ?? []);
            const existingAccessories = @json($product->productAccessories ?? []);

            function addFilterRow(filterData = null) {
                let tpl = $('#tplFilterRow').html().replace(/__INDEX__/g, filterIndex++);
                const $row = $(tpl);

                // Nếu có dữ liệu từ server, điền vào
                if (filterData) {
                    $row.find('select[name*="[product_filter_id]"]').val(filterData.product_filter_id);
                    $row.find('input[name*="[maintenance_schedule]"]').val(filterData.maintenance_schedule ?? 6);
                    $row.find('input[name*="[quantity]"]').val(filterData.quantity ?? 1);
                    $row.find('input[name*="[is_active]"]').prop('checked', filterData.is_active);
                    if (filterData.id) {
                        $row.find('input[name*="[id]"]').val(filterData.id);
                    }
                }

                $('#filtersBody').append($row);
                applySelect2($row);
                initRowThumb($row);
            }

            function addAccessoryRow(accessoryData = null) {
                let tpl = $('#tplAccessoryRow').html().replace(/__INDEX__/g, accessoryIndex++);
                const $row = $(tpl);

                // Nếu có dữ liệu từ server, điền vào
                if (accessoryData) {
                    $row.find('select[name*="[product_accessory_id]"]').val(accessoryData.product_accessory_id);
                    $row.find('input[name*="[quantity]"]').val(accessoryData.quantity ?? 1);
                    $row.find('input[name*="[is_active]"]').prop('checked', accessoryData.is_active);
                    if (accessoryData.id) {
                        $row.find('input[name*="[id]"]').val(accessoryData.id);
                    }
                }

                $('#accessoriesBody').append($row);
                applySelect2($row);
                initRowThumb($row);
            }

            // Render dữ liệu hiện có từ server
            function renderExistingData() {
                // Render filters
                existingFilters.forEach(filter => {
                    addFilterRow(filter);
                });

                // Render accessories
                existingAccessories.forEach(accessory => {
                    addAccessoryRow(accessory);
                });
            }

            $('#btnAddFilter').on('click', addFilterRow);
            $('#btnAddAccessory').on('click', addAccessoryRow);
            $(document).on('click', '.btnRemoveRow', function() {
                $(this).closest('tr').remove();
            });

            // Khởi tạo select2 cho các select hiện có
            applySelect2();

            // Render dữ liệu hiện có từ server
            renderExistingData();

            /* ========= CKEditor (nếu dùng) ========= */
            if (typeof CKEDITOR !== 'undefined') {
                $('.ckeditor').each(function() {
                    if (!this.id) this.id = 'ck_' + Math.random().toString(36).slice(2);
                    CKEDITOR.replace(this.id);
                });
            }

            // ========= THUMB IMAGE HANDLING =========

            // Function để update ảnh cho một row cụ thể
            function updateRowThumb($row) {
                const $select = $row.find('select.select2');
                const $thumbCell = $row.find('td').eq(1); // Cột thứ 2 (index 1) chứa ảnh

                if (!$select.length || !$thumbCell.length) {
                    return;
                }

                const selectedValue = $select.val();
                if (selectedValue) {
                    const $selectedOption = $select.find('option:selected');
                    const url = $selectedOption.data('thumb');

                    if (url && url !== '' && url !== 'undefined' && url !== 'null') {
                        $thumbCell.html('<img class="thumb" src="' + url + '" alt="thumb" />');
                    } else {
                        $thumbCell.html('<span class="text-muted">Chưa chọn</span>');
                    }
                } else {
                    $thumbCell.html('<span class="text-muted">Chưa chọn</span>');
                }
            }

            // Function để khởi tạo thumb cho một row
            function initRowThumb($row) {
                const $select = $row.find('select.select2');

                if (!$select.length) {
                    return;
                }

                // Update ảnh hiện tại
                updateRowThumb($row);

                // Bind event handlers
                $select.off('change.thumb select2:select.thumb select2:clear.thumb');
                $select.on('change.thumb', function() {
                    updateRowThumb($row);
                });
                $select.on('select2:select.thumb select2:clear.thumb', function() {
                    updateRowThumb($row);
                });
            }

            // Khởi tạo thumb cho tất cả rows hiện có
            function initAllRows() {
                $('#filtersBody tr, #accessoriesBody tr').each(function() {
                    initRowThumb($(this));
                });
            }

            // Khởi tạo lại sau khi DOM ready hoàn toàn
            setTimeout(initAllRows, 100);

            // Debug functions
            window.forceUpdateAllThumbs = initAllRows;
            window.debugThumbs = function() {
                console.log('=== DEBUG THUMBS ===');
                $('#filtersBody tr, #accessoriesBody tr').each(function(index) {
                    const $row = $(this);
                    const $select = $row.find('select.select2');
                    const $thumbCell = $row.find('td').eq(1);
                    const selectedValue = $select.val();
                    const $selectedOption = $select.find('option:selected');
                    const url = $selectedOption.data('thumb');

                    console.log(`Row ${index}:`, {
                        hasSelect: $select.length > 0,
                        selectedValue: selectedValue,
                        url: url,
                        thumbCellContent: $thumbCell.html()
                    });
                });
                console.log('=== END DEBUG ===');
            };

        });
    </script>
@endsection
