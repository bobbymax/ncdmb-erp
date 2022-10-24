<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $contracts = Contract::latest()->get();

        if ($contracts->count < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $contracts,
            'status' => 'success',
            'message' => 'List of Contracts'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bid_id' => 'required|integer',
            'organization_id' => 'required|integer',
            'path' => 'required|string',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $contract = Contract::create([
            'bid_id' => $request->bid_id,
            'organization_id' => $request->organization_id,
            'path' => $request->path,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'data' => $contract,
            'status' => 'success',
            'message' => 'Contract has been awarded successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $contract
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($contract)
    {
        $contract = Contract::find($contract);

        if (! $contract) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $contract,
            'status' => 'success',
            'message' => 'Contract Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $contract
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($contract)
    {
        $contract = Contract::find($contract);

        if (! $contract) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $contract,
            'status' => 'success',
            'message' => 'Contract Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $contract)
    {
        $validator = Validator::make($request->all(), [
            'bid_id' => 'required|integer',
            'organization_id' => 'required|integer',
            'path' => 'required|string',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $contract = Contract::find($contract);

        if (! $contract) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $contract->update([
            'bid_id' => $request->bid_id,
            'organization_id' => $request->organization_id,
            'path' => $request->path,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'data' => $contract,
            'status' => 'success',
            'message' => 'Contract has been updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $contract
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($contract)
    {
        $contract = Contract::find($contract);

        if (! $contract) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $contract;
        $contract->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Contract has been deleted successfully!'
        ], 200);
    }
}
