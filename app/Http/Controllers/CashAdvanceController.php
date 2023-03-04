<?php

namespace App\Http\Controllers;

use App\Models\CashAdvance;
use App\Models\Retirement;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashAdvanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cashAdvance = CashAdvance::latest()->get();

        if ($cashAdvance->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $cashAdvance,
            'status' => 'success',
            'message' => 'Batches list'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'code' => 'required|string|unique:cash_advances',
            'starts' => 'required|date',
            'ends' => 'required|date',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $cashAdvance = CashAdvance::create([
            'description' => $request->description,
            'code' => $request->code,
            'amount' => $request->amount,
            'user_id' => $request->user_id,
            'starts' => Carbon::parse($request->starts),
            'ends' => Carbon::parse($request->ends),
        ]);

        return response()->json([
            'data' => $cashAdvance,
            'status' => 'success',
            'message' => 'Cash Advance has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param CashAdvance $cashAdvance
     * @return JsonResponse
     */
    public function show($cashAdvance): JsonResponse
    {
        $cashAdvance = CashAdvance::find($cashAdvance);

        if (! $cashAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $cashAdvance,
            'status' => 'success',
            'message' => 'Cash Advance Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CashAdvance $cashAdvance
     * @return JsonResponse
     */
    public function edit($cashAdvance): JsonResponse
    {
        $cashAdvance = CashAdvance::find($cashAdvance);

        if (! $cashAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $cashAdvance,
            'status' => 'success',
            'message' => 'Cash Advance Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param CashAdvance $cashAdvance
     * @return JsonResponse
     */
    public function update(Request $request, $cashAdvance): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'code' => 'required|string|unique:cash_advances',
            'starts' => 'required|date',
            'ends' => 'required|date',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $cashAdvance = CashAdvance::find($cashAdvance);

        if (! $cashAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $cashAdvance->update([
            'description' => $request->description,
            'code' => $request->code,
            'amount' => $request->amount,
            'user_id' => $request->user_id,
            'starts' => Carbon::parse($request->starts),
            'ends' => Carbon::parse($request->ends),
        ]);

        return response()->json([
            'data' => $cashAdvance,
            'status' => 'success',
            'message' => 'Cash Advance details updated successfully!!'
        ], 200);
    }

    public function makeRetirement(Request $request, $cashAdvance): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'retirements' => 'required|array',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $cashAdvance = CashAdvance::find($cashAdvance);

        if (! $cashAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        foreach ($request->retirements as $retirement)
        {
            Retirement::create([
                'cash_advance_id' => $cashAdvance->id,
                'starts' => Carbon::parse($retirement['starts']),
                'ends' => Carbon::parse($retirement['ends']),
                'description' => $retirement['description'],
                'amount' => $retirement['amount'],
            ]);
        }

        $cashAdvance->update([
            'spent_amount' => $request->amount,
            'status' => 'retired',
            'closed' => true
        ]);

        return response()->json([
            'data' => $cashAdvance,
            'status' => 'success',
            'message' => 'Cash Advance retirements have been logged successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CashAdvance $cashAdvance
     * @return JsonResponse
     */
    public function destroy($cashAdvance): JsonResponse
    {
        $cashAdvance = CashAdvance::find($cashAdvance);

        if (! $cashAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $old = $cashAdvance;
        $cashAdvance->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Cash Advance details deleted successfully!!'
        ], 200);
    }
}
