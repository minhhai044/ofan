@extends('admin.layouts.master')
@section('style')
    <style>
        .avatar-img {
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
                <h4 class="mb-sm-0 font-size-18">Danh sách nhân viên</h4>

                <div class="page-title-right">
                    <a href="{{ route('users.create') }} "><button class="btn btn-primary btn-sm float-end mb-2 me-3">Thêm
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
                            placeholder="Họ và tên" autocomplete="off">
                    </div>
                </div>

                {{-- SĐT --}}
                <div class="col-sm-3 col-md-2">
                    {{-- <label class="form-label mb-1">Số điện thoại</label> --}}
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                        <input type="text" name="phone" value="{{ request('phone') }}" class="form-control"
                            placeholder="Số điện thoại" inputmode="numeric" pattern="[0-9]{8,12}" autocomplete="off">
                    </div>
                </div>
                {{-- Branchs --}}
                <div class="col-sm-3 col-md-2">

                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <select name="branch_id" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ request('branch_id', '') === '' ? 'selected' : '' }}>Tất cả</option>
                            @foreach ($branches as $branch)
                                <option @selected($branch->id == request('branch_id', '') ) value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach

                        </select>
                    </div>

                </div>
                {{-- Trạng thái --}}
                <div class="col-sm-3 col-md-2">
                    {{-- <label class="form-label mb-1">Trạng thái</label> --}}
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-align-justify"></i></span>
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
                                <th class="text-center">Họ và tên</th>
                                <th class="text-center">Avatar</th>
                                <th class="text-center">Số điện thoại</th>
                                <th class="text-center">Địa chỉ</th>
                                <th class="text-center">Chi nhánh</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($users->count() > 0)
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td><a target="_blank" href="{{ getImageStorage($user->avatar) }}"><img
                                                    class="avatar-img" src="{{ getImageStorage($user->avatar) }}"
                                                    alt=""></a>
                                        </td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->address }}</td>
                                        <td>
                                            <span class="badge badge-pill badge-soft-success font-size-11">{{ $user->branch->name ?? 'Không tìm thấy !!!' }}</span>
                                        </td>
                                        <td>
                                            @if ($user->is_active == 1)
                                                <span class="badge badge-pill badge-soft-success font-size-11">Kích
                                                    hoạt</span>
                                            @else
                                                <span class="badge badge-pill badge-soft-danger font-size-11">Chưa kích
                                                    hoạt</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('users.edit', $user->slug) }}"
                                                class="btn btn-warning btn-sm"><i class="bx bxs-edit"></i></a>
                                            <form action="{{ route('users.updateStatus', $user->id) }}" method="POST"
                                                style="display: inline-block">
                                                @csrf
                                                @method('PUT')
                                                @if ($user->is_active)
                                                    <input type="text" hidden name="is_active" value="0">
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="bx bx-trash"></i></button>
                                                @else
                                                    <input type="text" hidden name="is_active" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm"><i
                                                            class="bx bx-rotate-right"></i></button>
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8">Người dùng không tồn tại</td>
                                </tr>
                            @endif

                        </tbody>

                    </table>
                    {!! $users->appends(Request::all())->links() !!}
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection

@section('script')
@endsection





































{{-- @extends('admin.layouts.master')
@section('style')
    <style>
        .avatar-img {
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
            <form method="GET" class="row g-2 align-items-end mb-2">
                <div class="col-sm-3 col-md-2">
                   
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                            placeholder="Họ và tên" autocomplete="off">
                    </div>
                </div>

               
                <div class="col-sm-3 col-md-2">
                   
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                        <input type="text" name="phone" value="{{ request('phone') }}" class="form-control"
                            placeholder="Số điện thoại" inputmode="numeric" pattern="[0-9]{8,12}" autocomplete="off">
                    </div>
                </div>

               
                <div class="col-sm-3 col-md-2">
                  
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-align-justify"></i></span>
                        <select name="is_active" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ request('is_active', '') === '' ? 'selected' : '' }}>Tất cả</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Ngừng hoạt động
                            </option>
                        </select>
                    </div>

                </div>

              
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
                                <th class="text-center">Họ và tên</th>
                                <th class="text-center">Avatar</th>
                                <th class="text-center">Số điện thoại</th>
                                <th class="text-center">Địa chỉ</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($users->count() > 0)
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td><a target="_blank" href="{{ getImageStorage($user->avatar) }}"><img class="avatar-img" src="{{ getImageStorage($user->avatar) }}"
                                                alt=""></a>
                                        </td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->address }}</td>
                                        <td>
                                            @if ($user->is_active == 1)
                                                <span class="badge badge-pill badge-soft-success font-size-11">Kích
                                                    hoạt</span>
                                            @else
                                                <span class="badge badge-pill badge-soft-danger font-size-11">Chưa kích
                                                    hoạt</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('users.edit', $user->slug) }}"
                                                class="btn btn-warning btn-sm"><i class="bx bxs-edit"></i></a>
                                            <form action="{{ route('users.updateStatus', $user->id) }}" method="POST"
                                                style="display: inline-block">
                                                @csrf
                                                @method('PUT')
                                                @if ($user->is_active)
                                                    <input type="text" hidden name="is_active" value="0">
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="bx bx-trash"></i></button>
                                                @else
                                                    <input type="text" hidden name="is_active" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm"><i
                                                            class="bx bx-rotate-right"></i></button>
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">Người dùng không tồn tại</td>
                                </tr>
                            @endif

                        </tbody>

                    </table>
                    {!! $users->appends(Request::all())->links() !!}
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection

@section('script')
@endsection --}}
