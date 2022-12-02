<?php

namespace App\Http\Controllers;

use App\Http\Resources\FundResource;
use App\Models\Fund;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class FundController extends Controller
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
        $funds = Fund::with('subBudgetHead')->where('year', 2022)->latest()->get();

        if ($funds->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => FundResource::collection($funds),
            'status' => 'success',
            'message' => 'Sub-Budget Credit Lists'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
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
            'sub_budget_head_id' => 'required|integer',
            'approved_amount' => 'required|integer',
            'year' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $fund = Fund::create([
            'sub_budget_head_id' => $request->sub_budget_head_id,
            'approved_amount' => $request->approved_amount,
            'actual_balance' => $request->approved_amount,
            'booked_balance' => $request->approved_amount,
            'year' => $request->year
        ]);

        return response()->json([
            'data' => new FundResource($fund),
            'status' => 'success',
            'message' => 'Funds have been added to this Sub-Budget successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Fund  $fund
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($fund)
    {
        $fund = Fund::find($fund);
        if (! $fund) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => new FundResource($fund),
            'status' => 'success',
            'message' => 'Sub-Budget details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Fund  $fund
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($fund)
    {
        $fund = Fund::find($fund);
        if (! $fund) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => new FundResource($fund),
            'status' => 'success',
            'message' => 'Sub-Budget details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fund  $fund
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $fund)
    {
        $validator = Validator::make($request->all(), [
            'sub_budget_head_id' => 'required|integer',
            'approved_amount' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $fund = Fund::find($fund);

        if (! $fund) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $fund->update([
            'sub_budget_head_id' => $request->sub_budget_head_id,
            'approved_amount' => $request->approved_amount,
            'actual_balance' => $request->approved_amount,
            'booked_balance' => $request->approved_amount
        ]);

        return response()->json([
            'data' => new FundResource($fund),
            'status' => 'success',
            'message' => 'Funds have been updated to this Sub-Budget successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Fund  $fund
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($fund)
    {
        $fund = Fund::find($fund);

        if (! $fund) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        $old = $fund;
        $fund->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Sub-Budget funds deleted successfully!'
        ], 200);
    }
}
