@extends('admin.layouts.master')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Danh sách người dùng</h4>

                <div class="page-title-right">
                    <a href="{{ route('users.create') }} "><button class="btn btn-primary btn-sm float-end mb-2 me-3">Thêm
                            mới</button></a>
                </div>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table table-bordered text-center dt-responsive  nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th class="text-center">STT</th>
                                <th class="text-center">Họ và tên</th>
                                <th class="text-center">Avatar</th>
                                <th class="text-center">Số điện thoại</th>
                                <th class="text-center">Địa chỉ</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td><img src="{{ getImageStorage($user->avatar) }}" width="50%" alt=""></td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->address }}</td>
                                    <td>
                                        @if ($user->is_active == 1)
                                            <span class="badge badge-pill badge-soft-success font-size-11">Kích hoạt</span>
                                        @else
                                            <span class="badge badge-pill badge-soft-danger font-size-11">Chưa kích
                                                hoạt</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('users.edit', $user->id) }}"
                                            class="btn btn-warning btn-sm">Sửa</a>
                                        {{-- <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            style="display: inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                    {!!  $users->appends(Request::all())->links() !!}
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection

@section('script')
@endsection
