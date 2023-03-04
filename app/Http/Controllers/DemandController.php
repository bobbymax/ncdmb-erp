<?php

namespace App\Http\Controllers;

use App\Http\Resources\DemandResource;
use App\Models\Batch;
use App\Models\Demand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DemandController extends Controller
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
    public function index(): \Illuminate\Http\JsonResponse
    {
        $demands = Demand::where('isArchived', 0)->latest()->get();

        if ($demands->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'warning',
                'message' => 'No data found!!'
            ], 200);
        }

        return response()->json([
            'data' => DemandResource::collection($demands),
            'status' => 'success',
            'message' => 'List of Reversal Requests'
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
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'department_id' => 'required|integer',
            'batch_id' => 'required|integer',
            'description' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $demand = Demand::create([
            'user_id' => $request->user_id,
            'department_id' => $request->department_id,
            'batch_id' => $request->batch_id,
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => new DemandResource($demand),
            'status' => 'success',
            'message' => 'Reversal Request has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $demand
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($demand): \Illuminate\Http\JsonResponse
    {
        $demand = Demand::find($demand);

        if (! $demand) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new DemandResource($demand),
            'status' => 'success',
            'message' => 'Demand details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $demand
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($demand): \Illuminate\Http\JsonResponse
    {
        $demand = Demand::find($demand);

        if (! $demand) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new DemandResource($demand),
            'status' => 'success',
            'message' => 'Demand details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $demand
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $demand): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'batch_id' => 'required|integer',
            'description' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $demand = Demand::find($demand);

        if (! $demand) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $demand->update([
            'batch_id' => $request->batch_id,
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => new DemandResource($demand),
            'status' => 'success',
            'message' => 'Reversal Request has been updated successfully!'
        ], 200);
    }

    public function reversalResponse(Request $request, $demand): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|max:255|in:approved,denied',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $demand = Demand::find($demand);

        if (! $demand) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $demand->update([
            'status' => $request->status,
            'remark' => $request->remark
        ]);

        if ($request->status === "approved") {
            $batch = Batch::find($demand->batch_id);

            if ($batch) {
                foreach($batch->expenditures as $expenditure) {
                    $fund = $expenditure->fund();

                    if ($fund) {
                        $fund->booked_expenditure -= $expenditure->amount;
                        $fund->booked_balance += $expenditure->amount;
                        $fund->save();
                    }

                    $expenditure->status = "reversed";
                    $expenditure->batch_id = 0;
                    $expenditure->save();

                    if ($expenditure->claim !== null) {
                        $expenditure->claim->status = "registered";
                        $expenditure->claim->save();
                    }
                }

                $batch->delete();
            }

            $demand->update([
                'isArchived' => true
            ]);
        }

        return response()->json([
            'data' => new DemandResource($demand),
            'status' => 'success',
            'message' => 'Request has been approved and Batch has been reversed successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $demand
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($demand): \Illuminate\Http\JsonResponse
    {
        $demand = Demand::find($demand);

        if (! $demand) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $old = $demand;
        $demand->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Demand details'
        ], 200);
    }
}
