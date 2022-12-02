<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TrainingController extends Controller
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
        $trainings = Training::latest()->get();

        if ($trainings->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $trainings,
            'status' => 'success',
            'message' => 'List of Trainings'
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
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $training = Training::create([
            'title' => $request->title,
            'label' => Str::slug($request->title),
        ]);

        return response()->json([
            'data' => $training,
            'status' => 'success',
            'message' => 'Training Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $training
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($training): \Illuminate\Http\JsonResponse
    {
        $training = Training::find($training);

        if (! $training) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $training,
            'status' => 'success',
            'message' => 'Training Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $training
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($training): \Illuminate\Http\JsonResponse
    {
        $training = Training::find($training);

        if (! $training) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $training,
            'status' => 'success',
            'message' => 'Training Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $training
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $training): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $training = Training::find($training);

        if (! $training) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $training->update([
            'title' => $request->title,
            'label' => Str::slug($request->title),
        ]);

        return response()->json([
            'data' => $training,
            'status' => 'success',
            'message' => 'Training has been Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $training
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($training): \Illuminate\Http\JsonResponse
    {
        $training = Training::find($training);

        if (! $training) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $training;
        $training->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Training has been deleted successfully!'
        ], 200);
    }
}
