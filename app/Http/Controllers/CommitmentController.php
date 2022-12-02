<?php

namespace App\Http\Controllers;

use App\Models\Commitment;
use App\Models\Milestone;
use App\Models\Target;
use App\Models\Timeline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommitmentController extends Controller
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
        $commitments = Commitment::latest()->get();

        if ($commitments->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $commitments,
            'status' => 'success',
            'message' => 'List of Tasks and Targets'
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
            'targets' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $commitment = Commitment::create([
            'user_id' => auth()->user()->id,
        ]);

        if (! $commitment) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Something went wrong!!'
            ], 500);
        }

        foreach ($request->targets as $obj) {
            $target = Target::create([
                'commitment_id' => $commitment->id,
                'objectives' => $obj['objectives'],
                'measure' => $obj['measure'],
                'weight' => $obj['weight'],
                'target' => $obj['target'],
            ]);

            if ($target) {
                foreach ($obj['milestones'] as $value) {
                    $milestone = new Milestone;
                    $milestone->description = $value['description'];
                    $milestone->percentage_completion = $value['percentage_completion'];
                    $milestone->period = $value['period'];
                    $milestone->due_date = Carbon::parse($value['due_date']);

                    $target->milestones()->save($milestone);

                    $timeline = new Timeline;
                    $milestone->timeline()->save($timeline);
                }
            }
        }

        return response()->json([
            'data' => $commitment,
            'status' => 'success',
            'message' => 'Task & Target saved successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $commitment
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($commitment): \Illuminate\Http\JsonResponse
    {
        $commitment = Commitment::find($commitment);

        if (! $commitment) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $commitment,
            'status' => 'success',
            'message' => 'Commitment Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $commitment
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($commitment): \Illuminate\Http\JsonResponse
    {
        $commitment = Commitment::find($commitment);

        if (! $commitment) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $commitment,
            'status' => 'success',
            'message' => 'Commitment Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $commitment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $commitment): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'result' => 'required|string|in:outstanding,very-good,good,fair,poor',
            'remark' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $commitment = Commitment::find($commitment);

        if (! $commitment) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $commitment->update([
            'result' => $request->result,
            'remark' => $request->remark
        ]);

        return response()->json([
            'data' => $commitment,
            'status' => 'success',
            'message' => 'Commitment Details Updated Successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $commitment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($commitment): \Illuminate\Http\JsonResponse
    {
        $commitment = Commitment::find($commitment);

        if (! $commitment) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $commitment;
        $commitment->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Commitment Deleted Successfully!'
        ], 200);
    }
}
