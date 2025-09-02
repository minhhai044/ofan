@extends('admin.layouts.master')

@section('style')
    <style>
        .table thead th {
            vertical-align: middle;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6 !important
        }

        .table td,
        .table th {
            vertical-align: middle
        }

        .thumb {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #eee;
            background: #fafafa
        }

        .action-cell {
            white-space: nowrap
        }

        .action-cell form {
            display: inline-block;
            margin: 0
        }

        .btn-action {
            padding: .25rem .5rem
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Danh mục sản phẩm</h4>
                <div class="page-title-right">
                    <a href="{{ route('product_categories.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus"></i> Thêm mới
                    </a>
                </div>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-12">
            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-sm-4 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bx bx-rename"></i></span>
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                            placeholder="Tên danh mục">
                    </div>
                </div>

                <div class="col-sm-4 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bx bx-toggle-left"></i></span>
                        <select name="is_active" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="" {{ request('is_active', '') === '' ? 'selected' : '' }}>Tất cả</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Ngừng</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3 d-flex gap-1">
                    <button class="btn btn-primary btn-sm" type="submit"><i class="bx bx-filter-alt"></i></button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm"><i
                            class="bx bx-revision"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center nowrap w-100">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Logo</th>
                        <th>Tên danh mục</th>
                        {{-- <th>Slug</th> --}}
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product_categories as $i => $c)
                        @php
                            $active = !empty($c['is_active']);
                            $img = $c['image'] ?? null;
                            $id = $c['id'];
                            $slug = $c['slug'] ?? '';
                            $name = $c['name'] ?? '';
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>

                                <a href="{{ getImageStorage($img) }}" target="_blank"> <img
                                        src="{{ getImageStorage($img) }}" class="thumb" alt="logo"></a>

                            </td>
                            <td class="fw-semibold">{{ $name }}</td>
                            {{-- <td><code>{{ $slug }}</code></td> --}}
                            <td>
                                {!! $active
                                    ? '<span class="badge bg-success">Hoạt động</span>'
                                    : '<span class="badge bg-secondary">Ngừng</span>' !!}
                            </td>
                            <td class="action-cell">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('product_categories.edit', $slug) }}"
                                        class="btn btn-warning btn-sm btn-action" title="Sửa">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form action="{{ route('product_categories.updateStatus', $id) }}" method="POST"
                                        class="d-inline-block">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="is_active" value="{{ $active ? 0 : 1 }}">
                                        <button class="btn {{ $active ? 'btn-danger' : 'btn-success' }} btn-sm btn-action"
                                            title="{{ $active ? 'Ngừng hoạt động' : 'Kích hoạt' }}">
                                            <i class="bx {{ $active ? 'bx-power-off' : 'bx-check' }}"></i>
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

            {!! $product_categories->appends(Request::all())->links() !!}

        </div>
    </div>
@endsection
