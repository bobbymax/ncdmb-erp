<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Distribution;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistributionController extends Controller
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
        $distributions = Distribution::latest()->get();

        if ($distributions->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No Data Found!!'
            ], 200);
        }

        return response()->json([
            'data' => $distributions,
            'status' => 'success',
            'message' => 'List of Requisitions'
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
            'department_id' => 'required|integer',
            'product_id' => 'required|integer',
            'floor' => 'required|integer',
            'quantity' => 'required|integer',
            'office' => 'required',
            'category' => 'required|string|max:255|in:department,staff',
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $distribution = new Distribution;
        $distribution->user_id = auth()->user()->id;
        $distribution->department_id = $request->department_id;
        $distribution->product_id = $request->product_id;
        $distribution->floor = $request->floor;
        $distribution->quantity = $request->quantity;
        $distribution->office = $request->office;
        $distribution->category = $request->category;
        $category = $request->category === "department" ? Department::find($request->category_id) : User::find($request->category_id);
        $category->packages()->save($distribution);

        return response()->json([
            'data' => $distribution,
            'status' => 'success',
            'message' => 'Distribution has been registered successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $distribution
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($distribution): \Illuminate\Http\JsonResponse
    {
        $distribution = Distribution::find($distribution);

        if (! $distribution) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        return response()->json([
            'data' => $distribution,
            'status' => 'success',
            'message' => 'Distribution details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $distribution
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($distribution): \Illuminate\Http\JsonResponse
    {
        $distribution = Distribution::find($distribution);

        if (! $distribution) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        return response()->json([
            'data' => $distribution,
            'status' => 'success',
            'message' => 'Distribution details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $distribution
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $distribution): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|integer',
            'status' => 'required|string|max:255|in:pending,collected,end-of-life,overdue'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $distribution = Distribution::find($distribution);

        if (! $distribution) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        $distribution->update([
            'department_id' => $request->department_id,
            'status' => $request->status,
            'product_id' => $request->product_id,
            'floor' => $request->floor,
            'quantity' => $request->quantity,
            'office' => $request->office
        ]);

        return response()->json([
            'data' => $distribution,
            'status' => 'success',
            'message' => 'Distribution has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $distribution
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($distribution): \Illuminate\Http\JsonResponse
    {
        $distribution = Distribution::find($distribution);

        if (! $distribution) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        $old = $distribution;
        $distribution->items()->delete();
        $distribution->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Distribution has been deleted successfully!!'
        ], 200);
    }
}
