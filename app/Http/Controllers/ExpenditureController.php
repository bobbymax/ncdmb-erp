<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExpenditureResource;
use App\Models\Expenditure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenditureController extends Controller
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
    public function index()
    {
        $expenditures = Expenditure::with(['subBudgetHead'])->latest()->get();

        if ($expenditures->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!!'
            ], 200);
        }

        return response()->json([
            'data' => ExpenditureResource::collection($expenditures),
            'status' => 'success',
            'message' => 'Expenditure List'
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
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sub_budget_head_id' => 'required|integer',
            'department_id' => 'required|integer',
            'status' => 'required|string|in:cleared,batched,queried,paid',
            'beneficiary' => 'required|string',
            'description' => 'required',
            'payment_type' => 'required|string|in:staff-payment,third-party',
            'type' => 'string|in:cash-advance,retirement,other',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $expenditure = Expenditure::create([
            'sub_budget_head_id' => $request->sub_budget_head_id,
            'department_id' => $request->department_id,
            'cash_advance_id' => $request->cash_advance_id,
            'user_id' => auth()->user()->id,
            'type' => $request->type,
            'payment_type' => $request->payment_type,
            'beneficiary' => $request->beneficiary,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => $request->status,
            'additional_info' => $request->additional_info
        ]);

        if ($expenditure && $expenditure->fund() !== null) {
            $fund = $expenditure->fund();
            $fund->booked_expenditure += $expenditure->amount;
            $fund->booked_balance -= $expenditure->amount;
            $fund->save();

            if ($expenditure->cash_advance_id > 0) {
                $expenditure->advance->status = "cleared";
                $expenditure->advance->save();
            }
        }

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Expenditure $expenditure
     * @return JsonResponse
     */
    public function show($expenditure)
    {
        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Expenditure $expenditure
     * @return JsonResponse
     */
    public function edit($expenditure)
    {
        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Expenditure $expenditure
     * @return JsonResponse
     */
    public function update(Request $request, $expenditure)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Expenditure $expenditure
     * @return JsonResponse
     */
    public function destroy($expenditure)
    {
        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        if ($expenditure->batch_id > 0) {
            return response()->json([
                'data' => $expenditure,
                'status' => 'error',
                'message' => 'You cannot delete an expenditure that has been batched already!'
            ], 422);
        }

        if ($expenditure->claim !== null) {
            $expenditure->claim->status = "registered";
            $expenditure->claim->save();
        }

        $fund = $expenditure->fund();

        if ($fund) {
            $fund->booked_expenditure -= $expenditure->amount;
            $fund->booked_balance += $expenditure->amount;
            $fund->save();
        }

        $old = $expenditure;
        $expenditure->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Expenditure has been deleted successfully!'
        ], 200);
    }
}
