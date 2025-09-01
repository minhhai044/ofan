@extends('admin.layouts.master')

@section('style')
    <style>
        .perm-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 12px;
            background: #fff;
        }

        .perm-title {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .perm-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
        }

        .sticky-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px 0;
            border-top: 1px solid #e9ecef;
            margin-top: 8px;
        }
    </style>
@endsection

@section('content')
    @php
        $grouped = [];
        foreach ($permissions as $p) {
            $parts = preg_split('/\s+/', $p->name, 2);
            $resource = $parts[1] ?? $p->name; // phần sau là "đối tượng"
            $grouped[$resource][] = $p; // gom theo đối tượng
        }
        $allSelected = count($rolePermissions) > 0 && count($rolePermissions) === $permissions->count();
    @endphp

    <div class="container-fluid">

        {{-- MỞ FORM: bọc toàn bộ checkbox bên trong --}}
        <form action="{{ route('permissions.update', $role) }}" method="POST" id="permForm" class="m-0">
            @csrf
            @method('PUT')

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0">Phân quyền cho vai trò: <strong>{{ $role->name }}</strong></h4>

                <div class="d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAll" {{ $allSelected ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="checkAll">Chọn tất cả</label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> Lưu cập nhật
                    </button>
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Quay lại
                    </a>
                </div>
            </div>

            {{-- Thông báo --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Danh sách quyền theo nhóm --}}
            <div class="row">
                <div class="col-12 col-lg-12">
                    @foreach ($grouped as $resource => $items)
                        @php
                            $checkedCount = collect($items)
                                ->filter(fn($p) => in_array($p->name, $rolePermissions))
                                ->count();
                            $groupAll = $checkedCount === count($items);
                            $groupId = 'group_' . \Illuminate\Support\Str::slug($resource, '_');
                        @endphp

                        <div class="perm-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="perm-title text-capitalize mb-0">
                                    {{ ucfirst($resource) }}
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input group-toggle" type="checkbox"
                                        id="{{ $groupId }}_all" data-group="{{ $groupId }}"
                                        {{ $groupAll ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $groupId }}_all">Chọn nhóm</label>
                                </div>
                            </div>

                            <div id="{{ $groupId }}" class="mt-2">
                                @foreach ($items as $perm)
                                    @php $id = 'perm_'.md5($perm->name); @endphp
                                    <div class="perm-item border-top">
                                        <div class="form-check m-0">
                                            <input class="form-check-input perm-checkbox" type="checkbox"
                                                id="{{ $id }}" name="permissions[]" value="{{ $perm->name }}"
                                                {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $id }}">
                                                {{ $perm->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- nút lưu phía dưới cho tiện --}}
                    <div class="sticky-actions">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-save"></i> Lưu cập nhật
                        </button>
                    </div>
                </div>
            </div>
        </form>
        {{-- ĐÓNG FORM Ở ĐÂY --}}
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAll');
            const permCheckboxes = Array.from(document.querySelectorAll('.perm-checkbox'));
            const groupToggles = Array.from(document.querySelectorAll('.group-toggle'));

            // Toggle tất cả
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    const checked = this.checked;
                    permCheckboxes.forEach(cb => cb.checked = checked);
                    groupToggles.forEach(gt => gt.checked = checked);
                });
            }

            // Toggle theo nhóm
            groupToggles.forEach(gt => {
                gt.addEventListener('change', function() {
                    const groupId = this.dataset.group;
                    const groupBox = document.getElementById(groupId);
                    const boxes = groupBox.querySelectorAll('.perm-checkbox');
                    boxes.forEach(cb => cb.checked = this.checked);
                    refreshMaster();
                });
            });

            // Khi check lẻ từng quyền → cập nhật group & master
            permCheckboxes.forEach(cb => cb.addEventListener('change', () => {
                groupToggles.forEach(gt => {
                    const groupId = gt.dataset.group;
                    const groupBox = document.getElementById(groupId);
                    const boxes = Array.from(groupBox.querySelectorAll('.perm-checkbox'));
                    gt.checked = boxes.length > 0 && boxes.every(b => b.checked);
                });
                refreshMaster();
            }));

            function refreshMaster() {
                if (!checkAll) return;
                checkAll.checked = permCheckboxes.length > 0 && permCheckboxes.every(cb => cb.checked);
            }
        });
    </script>
@endsection
