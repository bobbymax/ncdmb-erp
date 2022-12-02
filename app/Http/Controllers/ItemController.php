<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
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
        $items = Item::latest()->get();

        if ($items->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No Data Found!!'
            ], 200);
        }

        return response()->json([
            'data' => $items,
            'status' => 'success',
            'message' => 'List of Requisition Items'
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($item): \Illuminate\Http\JsonResponse
    {
        $item = Item::find($item);

        if (! $item) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        return response()->json([
            'data' => $item,
            'status' => 'success',
            'message' => 'Item details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($item): \Illuminate\Http\JsonResponse
    {
        $item = Item::find($item);

        if (! $item) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        return response()->json([
            'data' => $item,
            'status' => 'success',
            'message' => 'Item details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $item): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity_expected' => 'required|integer',
            'quantity_received' => 'required|integer',
            'status' => 'required|string|max:255|in:pending,collected,end-of-life,approved,denied',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $item = Item::find($item);

        if (! $item) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        $item->update([
            'product_id' => $request->product_id,
            'quantity_expected' => $request->quantity_expected,
            'quantity_received' => $request->quantity_received,
            'description' => $request->description,
            'urgency' => $request->urgency,
            'status' => $request->status,
        ]);

        if ($request->status === "approved") {
            $value = $item->product->quantity_expected - $item->quantity_received;
            $item->product->quantity_expected = $value;
            $item->product->inStock = $value > 0;
            $item->product->save();
        }

        return response()->json([
            'data' => $item,
            'status' => 'success',
            'message' => 'Item has been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($item): \Illuminate\Http\JsonResponse
    {
        $item = Item::find($item);

        if (! $item) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Wrong ID input'
            ], 422);
        }

        $old = $item;
        $item->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Item has been deleted successfully!!'
        ], 200);
    }
}
