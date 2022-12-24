<?php

namespace App\Http\Controllers;

use App\Models\Pillar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PillarController extends Controller
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
        $pillars = Pillar::latest()->get();

        if ($pillars->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $pillars,
            'status' => 'success',
            'message' => 'List of Pillars'
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
            'name' => 'required|string|max:255',
            'description' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $pillar = Pillar::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => $pillar,
            'status' => 'success',
            'message' => 'Pillar created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $pillar
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($pillar): \Illuminate\Http\JsonResponse
    {
        $pillar = Pillar::find($pillar);

        if (! $pillar) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $pillar,
            'status' => 'success',
            'message' => 'Pillar details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $pillar
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($pillar): \Illuminate\Http\JsonResponse
    {
        $pillar = Pillar::find($pillar);

        if (! $pillar) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => $pillar,
            'status' => 'success',
            'message' => 'Pillar details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $pillar
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $pillar): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $pillar = Pillar::find($pillar);

        if (! $pillar) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $pillar->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => $pillar,
            'status' => 'success',
            'message' => 'Pillar updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $pillar
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($pillar): \Illuminate\Http\JsonResponse
    {
        $pillar = Pillar::find($pillar);

        if (! $pillar) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $pillar;
        $pillar->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Pillar deleted successfully!!'
        ], 200);
    }
}
