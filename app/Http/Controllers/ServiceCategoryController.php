<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
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
    public function index()
    {
        $serviceCategories = ServiceCategory::latest()->get();

        if ($serviceCategories->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $serviceCategories,
            'status' => 'success',
            'message' => 'List of Service Categories'
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:service_categories'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $serviceCategory = ServiceCategory::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => $serviceCategory,
            'status' => 'success',
            'message' => 'Service Category has been created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param $serviceCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($serviceCategory)
    {
        $serviceCategory = ServiceCategory::find($serviceCategory);
        if (! $serviceCategory) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => $serviceCategory,
            'status' => 'success',
            'message' => 'Service Category details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $serviceCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($serviceCategory)
    {
        $serviceCategory = ServiceCategory::find($serviceCategory);
        if (! $serviceCategory) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }
        return response()->json([
            'data' => $serviceCategory,
            'status' => 'success',
            'message' => 'Service Category details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $serviceCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $serviceCategory)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $serviceCategory = ServiceCategory::find($serviceCategory);
        if (! $serviceCategory) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $serviceCategory->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => $serviceCategory,
            'status' => 'success',
            'message' => 'Service Category updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $serviceCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($serviceCategory)
    {
        $serviceCategory = ServiceCategory::find($serviceCategory);
        if (! $serviceCategory) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $serviceCategory;
        $serviceCategory->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Service Category has been deleted successfully!!'
        ], 200);
    }
}
