<?php

namespace App\Http\Controllers;

use App\Models\Responsibility;
use App\Models\Timeline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResponsibilityController extends Controller
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
        $responsibilities = Responsibility::latest()->get();

        if ($responsibilities->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $responsibilities,
            'status' => 'success',
            'message' => 'List of Responsibilities'
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
            'department_id' => 'required|integer',
            'pillar_id' => 'required|integer',
            'description' => 'required|min:4',
            'due_date' => 'required|date',
            'priority' => 'required|string|in:low,medium,high,very-high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $responsibility = Responsibility::create([
            'department_id' => $request->department_id,
            'pillar_id' => $request->pillar_id,
            'due_date' => Carbon::parse($request->due_date),
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        if (! $responsibility) {
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Responsibility not created'
            ], 500);
        }

        $timeline = new Timeline;
        $responsibility->timeline()->save($timeline);

        return response()->json([
            'data' => $responsibility,
            'status' => 'success',
            'message' => 'Responsibility created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $responsibility
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($responsibility): \Illuminate\Http\JsonResponse
    {
        $responsibility = Responsibility::find($responsibility);

        if (! $responsibility) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $responsibility,
            'status' => 'success',
            'message' => 'Responsibility details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $responsibility
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($responsibility): \Illuminate\Http\JsonResponse
    {
        $responsibility = Responsibility::find($responsibility);

        if (! $responsibility) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $responsibility,
            'status' => 'success',
            'message' => 'Responsibility details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $responsibility
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $responsibility): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer',
            'pillar_id' => 'required|integer',
            'description' => 'required|min:4',
            'due_date' => 'required|date',
            'priority' => 'required|string|in:low,medium,high,very-high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $responsibility = Responsibility::find($responsibility);

        if (! $responsibility) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $responsibility->update([
            'department_id' => $request->department_id,
            'pillar_id' => $request->pillar_id,
            'due_date' => Carbon::parse($request->due_date),
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        return response()->json([
            'data' => $responsibility,
            'status' => 'success',
            'message' => 'Responsibility updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $responsibility
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($responsibility): \Illuminate\Http\JsonResponse
    {
        $responsibility = Responsibility::find($responsibility);

        if (! $responsibility) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $responsibility;
        $responsibility->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Responsibility deleted successfully!!'
        ], 200);
    }
}
