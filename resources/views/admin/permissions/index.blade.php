@extends('admin.layouts.master')
@section('style')
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Danh sách vai trò</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table align-middle  dt-responsive nowrap w-100 table-bordered w-100 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Tên</th>
                                    <th>Quyền</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <td>


                                        {{ $loop->iteration }}

                                    </td>

                                    <td>
                                        {{ $role->name }}
                                    </td>
                                    <td>
                                        @if ($role->permissions->isNotEmpty())
                                            @foreach ($role->permissions->take(3) as $permission)
                                                <span class="badge rounded-pill bg-success">
                                                    {{ $permission->name }}
                                                </span>
                                            @endforeach

                                            @if ($role->permissions->count() > 3)
                                                <span class="badge rounded-pill bg-primary"> +
                                                    {{ $role->permissions->count() - 3 }} quyền </span>
                                            @endif
                                        @else
                                            <span class="badge bg-danger-subtle text-danger text-uppercase">Không có
                                                quyền</span>
                                        @endif
                                    </td>


                                    <td>
                                        <a href="{{ route('permissions.edit', $role) }}">
                                            <button title="xem" class="btn btn-warning btn-sm " type="button"><i
                                                    class="fas fa-edit"></i></button>
                                        </a>


                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
