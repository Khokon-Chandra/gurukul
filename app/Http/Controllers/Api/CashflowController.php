<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cashflow\StoreCashflowRequest;
use App\Http\Requests\Api\Cashflow\UpdateCashflowRequest;
use App\Http\Resources\Api\CashflowResource;
use App\Models\Cashflow;
use App\Trait\Authorizable;
use Illuminate\Http\Request;

class CashflowController extends Controller
{
    use Authorizable;

    public function index(Request $request)
    {
        $cashflows = Cashflow::latest()
            ->filter($request)
            ->paginate(AppConstant::PAGINATION);

        return CashflowResource::collection($cashflows);
    }


    /**
     * Create new Cashflow
     */
    public function store(StoreCashflowRequest $request)
    {
        try {
            $data = Cashflow::create($request->validated());

            return response()->json([
                'status'  => 'success',
                'message' => 'Cashflow created successfully',
                'data'    => $data
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }
    

    public function update(UpdateCashflowRequest $request, $id)
    {
        
        try {
            $cashflow = Cashflow::find($id);

            if (!$cashflow) throw new \Exception('Cashflow not found', 404);

            $cashflow->update($request->validated());

            return response()->json([
                'status'  => 'success',
                'message' => 'Cashflow updated successfully',
                'data'    => new CashflowResource($cashflow)
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }
}
