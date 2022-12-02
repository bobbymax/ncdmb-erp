<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClassificationController extends Controller
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
        $classifications = Classification::latest()->get();

        if ($classifications->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => $classifications,
            'status' => 'success',
            'message' => 'Category List'
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
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $classification = Classification::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => $classification,
            'status' => 'success',
            'message' => 'Classification Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $classification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($classification): \Illuminate\Http\JsonResponse
    {
        $classification = Classification::find($classification);

        if (! $classification) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $classification,
            'status' => 'success',
            'message' => 'Classification Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $classification
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($classification): \Illuminate\Http\JsonResponse
    {
        $classification = Classification::find($classification);

        if (! $classification) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => $classification,
            'status' => 'success',
            'message' => 'Classification Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $classification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $classification): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $classification = Classification::find($classification);

        if (! $classification) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $classification->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => $classification,
            'status' => 'success',
            'message' => 'Classification Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $classification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($classification): \Illuminate\Http\JsonResponse
    {
        $classification = Classification::find($classification);

        if (! $classification) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $classification;
        $classification->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Classification Deleted Successfully!'
        ], 200);
    }
}
