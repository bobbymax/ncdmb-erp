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
    public function index(): JsonResponse
    {
        $expenditures = Expenditure::latest()->get();

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
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sub_budget_head_id' => 'required|integer',
            'department_id' => 'required|integer',
            'status' => 'required|string|in:cleared,batched,queried,paid',
            'beneficiary' => 'required|string',
            'description' => 'required',
            'payment_type' => 'required|string|in:staff-payment,third-party',
            'type' => 'string|in:claim,touring-advance,other',
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
            'claim_id' => $request->claim_id,
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

            if ($expenditure->claim_id > 0) {
                $expenditure->claim->status = "cleared";
                $expenditure->claim->save();
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
     * @param $expenditure
     * @return JsonResponse
     */
    public function show($expenditure): JsonResponse
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
     * @param $expenditure
     * @return JsonResponse
     */
    public function edit($expenditure): JsonResponse
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
     * @param Request $request
     * @param $expenditure
     *
     * Clear Expenditure Payments
     *
     * @return JsonResponse
     */
    public function clearPayment(Request $request, $expenditure): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,cleared,paid',
            'approval_status' => 'required|string|in:pending,cleared,resolved',
            'stage' => 'required|string|in:budget-office,treasury,audit,accounts',
            'level' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $expenditure->update([
            'status' => $request->status,
            'approval_status' => $request->approval_status,
            'stage' => $request->stage,
            'level' => $request->level
        ]);

        $batch = $expenditure->batch;
        $expCount = $batch->expenditures->count();
        $exps = $batch->expenditures->where('approval_status', 'cleared');
        $expsPaid = $batch->expenditures->where('status', 'paid');

        if ($expCount == $exps->count()) {
            $batch->update([
                'status' => 'registered',
                'stage' => $request->stage
            ]);
        }

        if ($expCount == $expsPaid->count()) {
            $batch->update([
                'status' => 'paid'
            ]);
        }

        if ($expenditure->status === "paid") {
            $fund = $expenditure->subBudgetHead->fund;
            $fund->actual_expenditure += $expenditure->amount;
            $fund->actual_balance -= $expenditure->amount;
            $fund->save();

            if ($expenditure->claim_id > 0) {
                $claim = $expenditure->claim;

                $claim->status = "paid";
                $claim->save();
            }
        }

        $status = $expenditure->status === "paid" ? "paid" : "cleared";

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure has been marked as ' . $status . "!!!"
        ], 200);
    }

    public function queryExpenditure(Request $request, $expenditure): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,cleared,queried',
            'approval_status' => 'required|string|in:cleared,queried,resolved',
            'stage' => 'required|string|in:budget-office,treasury,audit',
            'remark' => 'required|string|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $expenditure->update([
            'status' => $request->status,
            'approval_status' => $request->approval_status,
            'stage' => $request->stage,
            'remark' => $request->remark
        ]);

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param $expenditure
     * @return JsonResponse
     */
    public function update(Request $request, $expenditure): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $fund = $expenditure->subBudgetHead->fund;

        if ($expenditure->amount > $request->amount) {
            $diff = $expenditure->amount - $request->amount;

            if ($fund->booked_balance < $diff) {
                return response()->json([
                    'data' => null,
                    'status' => 'warning',
                    'message' => 'There are no funds on this budget head!!!'
                ], 422);
            }

            $fund->booked_expenditure += $diff;
            $fund->booked_balance -= $diff;
            $fund->save();

        } else if ($expenditure->amount < $request->amount) {

            $diff = $request->amount - $expenditure->amount;

            $fund->booked_expenditure -= $diff;
            $fund->booked_balance += $diff;
            $fund->save();

        } else {
            return response()->json([
                'data' => new ExpenditureResource($expenditure),
                'status' => 'info',
                'message' => 'Amount is the same, so not update was rendered!!'
            ], 200);
        }

        $expenditure->update([
            'amount' => $request->amount,
        ]);

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $expenditure
     * @return JsonResponse
     */
    public function destroy($expenditure): JsonResponse
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
