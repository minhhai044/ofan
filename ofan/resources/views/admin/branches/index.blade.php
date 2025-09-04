@extends('admin.layouts.master')

@section('content')
    <style>
        /* Trang trí nhẹ cho table */
        .table thead th {
            vertical-align: middle;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6 !important;
        }

        .tree-cell {
            position: relative;
            padding-left: calc(8px + var(--lv, 0)*18px) !important;
            text-align: left !important;
            white-space: nowrap;
        }

        .tree-dot {
            width: 8px;
            height: 8px;
            display: inline-block;
            border-radius: 50%;
            background: #0d6efd;
            margin-right: 8px;
            vertical-align: middle;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .code-misa {
            font-size: 12px;
            color: #6c757d;
            border: 1px dashed #ced4da;
            padding: 2px 6px;
            border-radius: 6px;
            background: #fff;
        }

        .btn-action {
            padding: .25rem .5rem;
        }

        form .form-control,
        form .form-select {
            max-width: 180px;
            /* tuỳ chỉnh theo ý bạn */
        }
    </style>

    @php

        // $childrenMap = [];
        // foreach ($branches as $b) {
        //     $parent = $b['branch_id'] ?? 0;
        //     $childrenMap[$parent] = $childrenMap[$parent] ?? [];
        //     $childrenMap[$parent][] = $b;
        // }

        $ids = [];
        foreach ($branches as $b) {
            $ids[] = $b['id'];
        }
        $ids = array_flip($ids); // map id => index

        $childrenMap = [];
        foreach ($branches as $b) {
            // parent gốc: null -> 0
            $parent = $b['branch_id'] ?? 0;

            // NẾU cha không còn trong tập $branches (do lọc) => đưa lên gốc (0)
            if (!$parent || !isset($ids[$parent])) {
                $parent = 0;
            }

            $childrenMap[$parent] = $childrenMap[$parent] ?? [];
            $childrenMap[$parent][] = $b;
        }

        // Hàm render hàng theo dạng cây (đệ quy)
        $stt = 0;
        $renderRows = function ($parentId, $level) use (&$renderRows, &$childrenMap, &$stt) {
            $html = '';
            $list = $childrenMap[$parentId] ?? [];
            foreach ($list as $item) {
                $stt++;
                $badge = $item['is_active']
                    ? '<span class="badge bg-success">Hoạt động</span>'
                    : '<span class="badge bg-secondary">Ngừng hoạt động</span>';
                $lv = (int) $level;
                $misa = $item['code_misa']
                    ? '<span class="code-misa">MISA: ' . $item['code_misa'] . '</span>'
                    : '<span class="text-muted small">—</span>';

                $html .= '<tr>';
                $html .= '  <td class="text-center" style="width:70px">' . $stt . '</td>';
                $html .=
                    '  <td class="tree-cell" style="--lv:' .
                    $lv .
                    '">
                            <span class="tree-dot"></span>' .
                    $item['name'] .
                    '<div class="mt-1">' .
                    $misa .
                    '</div>
                        </td>';
                $html .= '  <td>' . $item['address'] . '</td>';
                $html .= '  <td class="text-center">' . $badge . '</td>';
                //             $html .=
                //                 '  <td >

        //         <a href="' .
                //                 route('branches.edit', $item['slug']) .
                //                 '" class="btn btn-warning btn-action" title="Sửa">
        //             <i class="bx bx-edit"></i>
        //         </a>

        //         <form action="' .
                //                 route('branches.updateStatus', $item['id']) .
                //                 '" method="POST" >
        //             ' .
                //                 csrf_field() .
                //                 method_field('PUT') .
                //                 '
        //             <input type="hidden" name="is_active" value="' .
                //                 ($item['is_active'] ? 0 : 1) .
                //                 '">
        //             <button type="submit" class="btn ' .
                //                 ($item['is_active'] ? 'btn-danger' : 'btn-success') .
                //                 ' btn-action"
        //                     title="' .
                //                 ($item['is_active'] ? 'Ngừng hoạt động' : 'Kích hoạt lại') .
                //                 '">
        //                 <i class="bx ' .
                //                 ($item['is_active'] ? 'bx-trash' : 'bx-check') .
                //                 '"></i>
        //             </button>
        //         </form>

        // </td>';
                $html .=
                    '  <td class="text-center action-cell">
    <div class="d-inline-flex align-items-center gap-1">
        <a href="' .
                    route('branches.edit', $item['slug']) .
                    '" class="btn btn-warning btn-action" title="Sửa">
            <i class="bx bx-edit"></i>
        </a>

        <form action="' .
                    route('branches.updateStatus', $item['id']) .
                    '" method="POST" class="m-0 p-0 d-inline-block">
            ' .
                    csrf_field() .
                    method_field('PUT') .
                    '
            <input type="hidden" name="is_active" value="' .
                    ($item['is_active'] ? 0 : 1) .
                    '">
            <button type="submit"
                class="btn ' .
                    ($item['is_active'] ? 'btn-danger' : 'btn-success') .
                    ' btn-action"
                title="' .
                    ($item['is_active'] ? 'Ngừng hoạt động' : 'Kích hoạt lại') .
                    '">
                <i class="bx ' .
                    ($item['is_active'] ? 'bx-trash' : 'bx-check') .
                    '"></i>
            </button>
        </form>
    </div>
</td>';

                // render các con
                $html .= $renderRows($item['id'], $level + 1);
            }
            return $html;
        };
        $renderedRows = $renderRows(0, 0);
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Danh sách chi nhánh</h4>
                <div class="page-title-right">
                    <a href="{{ route('branches.create') }}">
                        <button class="btn btn-primary btn-sm float-end mb-2 me-3">
                            <i class="bx bx-plus"></i> Thêm mới
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <form method="GET" class="row g-2 align-items-end mb-2">
                {{-- Từ khóa tên --}}
                <div class="col-sm-3 col-md-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-house-user"></i></span>
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                            placeholder="Chi nhánh" autocomplete="off">
                    </div>
                </div>

                {{-- Địa chỉ --}}
                <div class="col-sm-3 col-md-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        <input type="text" name="address" value="{{ request('address') }}" class="form-control"
                            placeholder="Địa chỉ" autocomplete="off">
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div class="col-sm-3 col-md-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-align-justify"></i></span>
                        <select name="is_active" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="" {{ request('is_active', '') === '' ? 'selected' : '' }}>Tất cả</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Ngừng</option>
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
                    <table class="table table-bordered text-center dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th class="text-center" style="width:70px">STT</th>
                                <th class="text-center">Tên chi nhánh</th>
                                <th class="text-center">Địa chỉ</th>
                                <th class="text-center" style="width:120px">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- {!! $renderRows(0, 0) !!} --}}


                            @if (trim($renderedRows) === '')
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-folder-open" style="font-size: 2rem;"></i>
                                            <div class="mt-1">Không có kết quả phù hợp</div>
                                            @php
                                                // Gợi ý nhanh để người dùng thử xóa bộ lọc
                                                $hasFilter =
                                                    request()->filled('name') ||
                                                    request()->filled('address') ||
                                                    request('is_active', '') !== '';
                                            @endphp
                                            @if ($hasFilter)
                                                <a href="{{ url()->current() }}"
                                                    class="btn btn-outline-secondary btn-sm mt-2">
                                                    Xóa bộ lọc
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @else
                                {!! $renderedRows !!}
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function confirmDelete(id) {
            if (confirm('Bạn chắc chắn muốn xoá chi nhánh #' + id + ' ?')) {
                // TODO: Gọi route xoá thực tế
                alert('Demo: Đã kích hoạt xoá ID ' + id);
            }
        }
    </script>
@endsection
