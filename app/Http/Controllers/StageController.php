<?php

namespace App\Http\Controllers;

use App\Http\Resources\StageResource;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StageController extends Controller
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
        $stages = Stage::latest()->get();

        if ($stages->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No Data Found!!'
            ], 200);
        }

        return response()->json([
            'data' => StageResource::collection($stages),
            'status' => 'success',
            'message' => 'List of Stages'
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
            'process_id' => 'required|integer',
            'role_id' => 'required|integer',
            'canEdit' => 'required',
            'canQuery' => 'required',
            'action' => 'required|string|max:255|in:sign,clear,post,confirm',
            'order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $stage = Stage::create([
            'process_id' => $request->process_id,
            'role_id' => $request->role_id,
            'canEdit' => $request->canEdit,
            'canQuery' => $request->canQuery,
            'action' => $request->action,
            'order' => $request->order
        ]);

        return response()->json([
            'data' => new StageResource($stage),
            'status' => "success",
            'message' => 'Stage has been created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $stage
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($stage): \Illuminate\Http\JsonResponse
    {
        $stage = Stage::find($stage);
        if (! $stage) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => new StageResource($stage),
            'status' => 'success',
            'message' => 'Stage details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $stage
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($stage): \Illuminate\Http\JsonResponse
    {
        $stage = Stage::find($stage);
        if (! $stage) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => new StageResource($stage),
            'status' => 'success',
            'message' => 'Stage details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $stage
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $stage): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'process_id' => 'required|integer',
            'role_id' => 'required|integer',
            'canEdit' => 'required',
            'canQuery' => 'required',
            'action' => 'required|string|max:255|in:sign,clear,post,confirm',
            'order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $stage = Stage::find($stage);

        if (! $stage) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $stage->update([
            'process_id' => $request->process_id,
            'role_id' => $request->role_id,
            'canEdit' => $request->canEdit,
            'canQuery' => $request->canQuery,
            'action' => $request->action,
            'order' => $request->order
        ]);

        return response()->json([
            'data' => new StageResource($stage),
            'status' => "success",
            'message' => 'Stage has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $stage
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($stage): \Illuminate\Http\JsonResponse
    {
        $stage = Stage::find($stage);

        if (! $stage) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $stage;
        $stage->delete();

        return response()->json([
            'data' => $old,
            'status' => "success",
            'message' => 'Stage has been deleted successfully!!'
        ], 200);
    }
}
