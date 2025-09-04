@extends('admin.layouts.master')
@section('style')
    <style>
        .thumb {
            width: 80%;
            /* ảnh chiếm 80% chiều rộng ô td */
            max-width: 80px;

            height: 80%;
            /* ảnh chiếm 80% chiều rộng ô td */
            max-height: 80px;

            /* tự co chiều cao giữ tỉ lệ */
            object-fit: cover;
            /* cắt ảnh vừa khung */
            /* border-radius: 50%; */
            /* nếu muốn tròn */
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
                <h4 class="mb-sm-0 font-size-18">Danh sách sản phẩm</h4>

                <div class="page-title-right">
                    <a href="{{ route('products.create') }} "><button class="btn btn-primary btn-sm float-end mb-2 me-3">Thêm
                            mới</button></a>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="GET" class="row g-2 align-items-end mb-2">
                <div class="col-sm-3 col-md-2">
                    {{-- <label class="form-label mb-1">Họ và tên</label> --}}
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                            placeholder="Tên sản phẩm" autocomplete="off">
                    </div>
                </div>


                {{-- Danh mục --}}
                <div class="col-sm-3 col-md-2">
                    {{-- <label class="form-label mb-1">Trạng thái</label> --}}
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-align-justify"></i></span>
                        <select name="product_category_id" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ request('product_category_id', '') === '' ? 'selected' : '' }}>Tất cả danh mục
                            </option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('product_category_id', '') == $category->id)>{{ $category->name }}
                                </option>
                            @endforeach
                            </option>
                        </select>
                    </div>

                </div>


                {{-- Trạng thái --}}
                <div class="col-sm-3 col-md-2">
                    {{-- <label class="form-label mb-1">Trạng thái</label> --}}
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bx bx-toggle-left"></i></span>
                        <select name="is_active" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ request('is_active', '') === '' ? 'selected' : '' }}>Tất cả</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Ngừng hoạt động
                            </option>
                        </select>
                    </div>

                </div>

                {{-- Nút --}}
                <div class="col-sm-3 col-md-2 d-flex gap-1">
                    <button class="btn btn-primary btn-sm" type="submit">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>

            </form>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered text-center dt-responsive  nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th class="text-center">STT</th>

                                <th class="text-center">Ảnh</th>
                                <th class="text-center">Tên sản phẩm</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @foreach ($product->images as $image)
                                            @if ($image['status'] == 1)
                                                <a href="{{ getImageStorage($image['image']) }}" target="_blank"> <img
                                                        src="{{ getImageStorage($image['image']) }}" class="thumb"
                                                        alt="logo"></a>
                                            @endif
                                        @endforeach


                                    </td>
                                    <td class="fw-semibold">{{ $product->name }}</td>

                                    <td>
                                        {!! $product->is_active
                                            ? '<span class="badge bg-success">Hoạt động</span>'
                                            : '<span class="badge bg-secondary">Ngừng</span>' !!}
                                    </td>
                                    <td class="action-cell">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('products.edit', $product->slug) }}"
                                                class="btn btn-warning btn-sm btn-action" title="Sửa">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <form action="{{ route('products.updateStatus', $product->id) }}"
                                                method="POST" class="d-inline-block">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="is_active"
                                                    value="{{ $product->is_active ? 0 : 1 }}">
                                                <button
                                                    class="btn {{ $product->is_active ? 'btn-danger' : 'btn-success' }} btn-sm btn-action"
                                                    title="{{ $product->is_active ? 'Ngừng hoạt động' : 'Kích hoạt' }}">
                                                    <i class="bx {{ $product->is_active ? 'bx-trash' : 'bx-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-folder-open" style="font-size:2rem"></i>
                                            <div class="mt-1">Không có kết quả phù hợp</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>
                    {!! $products->appends(Request::all())->links() !!}
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection

@section('script')
@endsection
