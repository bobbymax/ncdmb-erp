<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProcessResource;
use App\Models\Process;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProcessController extends Controller
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
        $processes = Process::latest()->get();

        if ($processes->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 404);
        }

        return response()->json([
            'data' => $processes,
            'status' => 'success',
            'message' => 'Brands List'
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:processes',
            'type' => 'required|string|max:255',
            'stages' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $process = Process::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'type' => $request->type
        ]);

        if (! $process) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Process was not created!!'
            ], 500);
        }

        foreach($request->stages as $stage) {
            Stage::create([
                'process_id' => $process->id,
                'role_id' => $stage['role_id'],
                'canEdit' => $stage['canEdit'],
                'canQuery' => $stage['canQuery'],
                'action' => $stage['action'],
                'order' => $stage['order']
            ]);
        }

        return response()->json([
            'data' => new ProcessResource($process),
            'status' => "success",
            'message' => 'Process has been created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $process
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($process): \Illuminate\Http\JsonResponse
    {
        $process = Process::find($process);
        if (! $process) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => new ProcessResource($process),
            'status' => 'success',
            'message' => 'Process details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $process
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($process): \Illuminate\Http\JsonResponse
    {
        $process = Process::find($process);
        if (! $process) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => new ProcessResource($process),
            'status' => 'success',
            'message' => 'Process details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $process
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $process): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:processes',
            'type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $process = Process::find($process);

        if (! $process) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $process->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'type' => $request->type
        ]);

        return response()->json([
            'data' => new ProcessResource($process),
            'status' => "success",
            'message' => 'Process has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $process
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($process): \Illuminate\Http\JsonResponse
    {
        $process = Process::find($process);

        if (! $process) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $process;
        $process->stages()->delete();
        $process->delete();

        return response()->json([
            'data' => $old,
            'status' => "success",
            'message' => 'Process has been deleted successfully!!'
        ], 200);
    }
}
