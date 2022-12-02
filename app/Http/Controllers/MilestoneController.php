<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Timeline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MilestoneController extends Controller
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
        $milestones = Milestone::latest()->get();

        if ($milestones->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $milestones,
            'status' => 'success',
            'message' => 'Tasks List'
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
            'project_id' => 'required|integer',
            'percentage_completion' => 'required|integer',
            'percentage_payment' => 'required|integer',
            'period' => 'required|integer',
            'description' => 'required',
            'due_date' => 'required|date',
            'measure' => 'required|string|in:days,weeks,months,years'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $milestone = Milestone::create([
            'project_id' => $request->project_id,
            'percentage_completion' => $request->percentage_completion,
            'percentage_payment' => $request->percentage_payment,
            'period' => $request->period,
            'description' => $request->description,
            'measure' => $request->measure,
            'due_date' => Carbon::parse($request->due_date)
        ]);

        $timeline = new Timeline;
        $milestone->timeline()->save($timeline);

        return response()->json([
            'data' => $milestone,
            'status' => 'success',
            'message' => 'Milestone Details have been created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $milestone
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($milestone): \Illuminate\Http\JsonResponse
    {
        $milestone = Milestone::find($milestone);

        if (! $milestone) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $milestone,
            'status' => 'success',
            'message' => 'Milestone details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $milestone
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($milestone): \Illuminate\Http\JsonResponse
    {
        $milestone = Milestone::find($milestone);

        if (! $milestone) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $milestone,
            'status' => 'success',
            'message' => 'Milestone details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $milestone
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $milestone): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer',
            'percentage_completion' => 'required|integer',
            'percentage_payment' => 'required|integer',
            'period' => 'required|integer',
            'description' => 'required',
            'due_date' => 'required|date',
            'measure' => 'required|string|in:days,weeks,months,years'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $milestone->update([
            'project_id' => $request->project_id,
            'percentage_completion' => $request->percentage_completion,
            'percentage_payment' => $request->percentage_payment,
            'period' => $request->period,
            'description' => $request->description,
            'measure' => $request->measure,
            'due_date' => Carbon::parse($request->due_date)
        ]);

        return response()->json([
            'data' => $milestone,
            'status' => 'success',
            'message' => 'Milestone Details have been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $milestone
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($milestone): \Illuminate\Http\JsonResponse
    {
        $milestone = Milestone::find($milestone);

        if (! $milestone) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $milestone;
        $milestone->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Milestone Details have been updated successfully!!'
        ], 200);
    }
}
