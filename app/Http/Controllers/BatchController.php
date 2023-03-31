<?php

namespace App\Http\Controllers;

use App\Http\Resources\BatchResource;
use App\Models\Batch;
use App\Models\Entry;
use App\Models\Expenditure;
use App\Models\Track;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class BatchController extends Controller
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
        $batches = Batch::latest()->get();

        if ($batches->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => BatchResource::collection($batches),
            'status' => 'success',
            'message' => 'Batches list'
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
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'expenditures' => 'required|array',
            'department_id' => 'required|integer',
            'amount' => 'required',
            'code' => 'required|string|unique:batches',
            'sub_budget_head_code' => 'required|string',
            'no_of_payments' => 'required|integer',
            'stage' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $batch = Batch::create([
            'user_id' => auth()->user()->id,
            'department_id' => $request->department_id,
            'code' => $request->code,
            'sub_budget_head_code' => $request->sub_budget_head_code,
            'amount' => $request->amount,
            'no_of_payments' => $request->no_of_payments,
            'stage' => $request->stage,
        ]);

        if ($batch) {
            foreach($request->expenditures as $value) {
                $expenditure = Expenditure::find($value['id']);

                if ($expenditure) {
                    $expenditure->batch_id = $batch->id;
                    $expenditure->status = "batched";
                    $expenditure->save();

                    if ($expenditure->cash_advance_id > 0) {
                        $expenditure->advance->status = "batched";
                        $expenditure->advance->save();
                    }
                }
            }
        }

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Batch created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param $batch
     * @return JsonResponse
     */
    public function show($batch): JsonResponse
    {
        $batch = Batch::find($batch);

        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Batch details'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param $batch
     * @return JsonResponse
     */
    public function collectBatch($batch): JsonResponse
    {
        $batch = Batch::where('code', $batch)->first();

        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Batch with this code was not found!!!'
            ], 422);
        }

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Batch details'
        ], 200);
    }

    /**
     * @param Request $request
     * @param $batch
     * @return JsonResponse
     */
    public function startProcess(Request $request, $batch): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer',
            'stage_id' => 'required|integer',
            'user_id' => 'required|integer',
            'code' => 'required|string|unique:tracks',
            'type' => 'required|string|max:255|in:inflow,outflow',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $batch = Batch::find($batch);

        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Batch with this code was not found!!!'
            ], 422);
        }

        if ($batch->track != null) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'This process has already been initiated'
            ], 422);
        }

        $track = new Track;
        $track->department_id = $request->department_id;
        $track->stage_id = $request->stage_id;
        $track->code = $request->code;

        if (!$batch->track()->save($track)) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Something went terribly wrong!!'
            ], 500);
        }

        $entry = new Entry;
        $entry->department_id = $track->department_id;
        $entry->user_id = $request->user_id;
        $entry->stage_id = $track->stage_id;
        $entry->type = $request->type;
        $entry->isActive = true;
        $track->entries()->save($entry);

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Payment Process for this batch has been initiated'
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $batch
     * @return JsonResponse
     */
    public function edit($batch): JsonResponse
    {
        $batch = Batch::find($batch);

        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Batch details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Batch $batch
     * @return Response
     */
    public function update(Request $request, Batch $batch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $batch
     * @return JsonResponse
     */
    public function destroy($batch): JsonResponse
    {
        $batch = Batch::find($batch);

        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

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

        $old = $batch;
        $batch->delete();
        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => "Batch has been reversed successfully!!"
        ], 200);
    }
}
