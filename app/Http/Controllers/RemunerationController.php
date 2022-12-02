<?php

namespace App\Http\Controllers;

use App\Http\Resources\RemunerationResource;
use App\Models\Remuneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RemunerationController extends Controller
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
        $remunerations = Remuneration::latest()->get();

        if ($remunerations->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'warning',
                'message' => 'No Data Found'
            ], 200);
        }

        return response()->json([
            'data' => RemunerationResource::collection($remunerations),
            'status' => 'success',
            'message' => 'List of Remunerations'
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parentId' => 'required',
            'type' => 'required|string|max:255|in:earnings,allowances',
            'category' => 'required|string|max:255|in:remittance,claims',
            'no_of_days' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $remuneration = Remuneration::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'parentId' => $request->parentId,
            'type' => $request->type,
            'category' => $request->category,
            'no_of_days' => $request->no_of_days,
        ]);

        return response()->json([
            'data' => new RemunerationResource($remuneration),
            'status' => 'success',
            'message' => 'Remuneration created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param $remuneration
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($remuneration): \Illuminate\Http\JsonResponse
    {
        $remuneration = Remuneration::find($remuneration);

        if (! $remuneration) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new RemunerationResource($remuneration),
            'status' => 'success',
            'message' => 'Remuneration details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $remuneration
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($remuneration): \Illuminate\Http\JsonResponse
    {
        $remuneration = Remuneration::find($remuneration);

        if (! $remuneration) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new RemunerationResource($remuneration),
            'status' => 'success',
            'message' => 'Remuneration details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $remuneration
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $remuneration): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parentId' => 'required',
            'type' => 'required|string|max:255|in:earnings,allowances',
            'category' => 'required|string|max:255|in:remittance,claims',
            'no_of_days' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $remuneration = Remuneration::find($remuneration);

        if (! $remuneration) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $remuneration->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'parentId' => $request->parentId,
            'type' => $request->type,
            'category' => $request->category,
            'no_of_days' => $request->no_of_days,
        ]);

        return response()->json([
            'data' => new RemunerationResource($remuneration),
            'status' => 'success',
            'message' => 'Remuneration details updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Remuneration $remuneration
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($remuneration): \Illuminate\Http\JsonResponse
    {
        $remuneration = Remuneration::find($remuneration);

        if (! $remuneration) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $old = $remuneration;
        $remuneration->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Remuneration details deleted successfully!'
        ], 200);
    }
}
