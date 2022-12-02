<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BrandController extends Controller
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
        $brands = Brand::latest()->get();

        if ($brands->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 404);
        }

        return response()->json([
            'data' => $brands,
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
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $brand = Brand::create([
            'name' => $request->name,
            'label' => Str::slug($request->name)
        ]);

        return response()->json([
            'data' => $brand,
            'status' => 'success',
            'message' => 'Brand has been created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $brand
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($brand): \Illuminate\Http\JsonResponse
    {
        $brand = Brand::find($brand);
        if (! $brand) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => $brand,
            'status' => 'success',
            'message' => 'Brand details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $brand
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($brand): \Illuminate\Http\JsonResponse
    {
        $brand = Brand::find($brand);
        if (! $brand) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => $brand,
            'status' => 'success',
            'message' => 'Brand details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $brand
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $brand): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $brand = Brand::find($brand);
        if (! $brand) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $brand->update([
            'name' => $request->name,
            'label' => Str::slug($request->name)
        ]);

        return response()->json([
            'data' => $brand,
            'status' => 'success',
            'message' => 'Brand has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $brand
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($brand): \Illuminate\Http\JsonResponse
    {
        $brand = Brand::find($brand);
        if (! $brand) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $brand;
        $brand->update();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Brand has been updated successfully!!'
        ], 200);
    }
}
