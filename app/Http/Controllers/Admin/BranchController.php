<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BranchRequest;
use App\Services\BranchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\FuncCall;

class BranchController extends Controller
{

    protected $BranchService;

    public function __construct(BranchService $BranchService)
    {
        $this->BranchService = $BranchService;
    }
    public function index(Request $request)
    {

        $branches  = $this->BranchService->getAllBranches($request->all(), []);

        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {

        $branches  = $this->BranchService->getAllBranches([], [], true);

        return view('admin.branches.create', compact('branches'));
    }

    public function store(BranchRequest $request)
    {
        try {

            $this->BranchService->storeBranch($request->validated());

            return redirect()->route('branches.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);
            return back()->with('error', 'Thao tác thành công !!!');
        }
    }

    public function edit(string $slug)
    {
        $branch = $this->BranchService->findBranch([], '', $slug);
        $branches  = $this->BranchService->getAllBranches([], [], true);

        return view('admin.branches.edit', compact('branch', 'branches'));
    }

    public function update(BranchRequest $request, string $id)
    {
        try {

            $this->BranchService->updateBranch($request->validated(), $id);

            return redirect()->route('branches.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {

            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);

            return back()->with('error', 'Thao tác thành công !!!');
        }
    }

    public function updateStatus(Request $request , string $id) {
        try {

            $this->BranchService->updateBranch($request->all(), $id);

            return redirect()->route('branches.index')->with('success', 'Thao tác thành công !!!');
        } catch (\Throwable $th) {

            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th->getMessage()]);

            return back()->with('error', 'Thao tác thành công !!!');
        }
    }
}
