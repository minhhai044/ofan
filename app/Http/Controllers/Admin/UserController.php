<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Services\BranchService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    protected $userService;
    protected $branchService;

    public function __construct(UserService $userService, BranchService $branchService)
    {
        $this->userService = $userService;
        $this->branchService = $branchService;
    }
    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers(20, $request->all(), []);
        return view('admin.users.index', compact('users'));
    }
    public function indexStaff(Request $request)
    {
        $filters = $request->all();
        $filters['role'] = 1;
        $branches = $this->branchService->getAllBranches([], [], true);

        $users = $this->userService->getAllUsers(20, $filters, []);
        return view('admin.users.index_staff', compact('users','branches'));
    }

    public function create()
    {

        $branches = $this->branchService->getAllBranches([], [], true);
        return view('admin.users.create', compact('branches'));
    }


    public function store(UserRequest $request)
    {
        try {
            $this->userService->storeUsers($request->validated());
            return redirect()->route('users.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác không thành công !!!');
        }
    }

    public function edit(string $slug)
    {
        $user = $this->userService->findUser([], '', $slug);
        $branches = $this->branchService->getAllBranches([], [], true);

        return view('admin.users.edit', compact('user', 'branches'));
    }

    public function update(UserRequest $request, string $id)
    {
        try {

            $this->userService->updateUser($request->validated(), $id);
            return redirect()->route('users.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác không thành công !!!');
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        try {

            $this->userService->updateUser($request->all(), $id);
            return redirect()->route('users.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác không thành công !!!');
        }
    }
}
