<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
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
        $products = Product::latest()->get();

        if ($products->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'warning',
                'message' => 'No data found!!',
            ], 200);
        }

        return response()->json([
            'data' => $products,
            'status' => 'success',
            'message' => 'Products List',
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
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:products',
            'brand_id' => 'required|integer',
            'classification_id' => 'required|integer',
            'quantity_expected' => 'required|integer',
            'categories' => 'required',
            'isDistributable' => 'required',
//            'photo' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [$validator->errors(), $request->all()],
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $product = Product::create([
            'brand_id' => $request->input('brand_id'),
            'classification_id' => $request->input('classification_id'),
            'title' => $request->input('title'),
            'label' => Str::slug($request->input('title')),
            'code' => $request->input('code'),
            'quantity_expected' => $request->input('quantity_expected'),
            'quantity_received' => $request->input('quantity_received'),
            'description' => $request->input('description'),
            'isDistributable' => $request->input('isDistributable'),
        ]);

        if ($product) {
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');

                $path = $file->store('products', 'public');
                $filename = $file->getClientOriginalName();
                $storage = Storage::disk('public')->put($filename, $path);

                if ($path) {
                    $image = new Image;
                    $image->name = $filename;
                    $image->size = $file->getSize();
                    $image->type = $file->getClientMimeType();
                    $image->path = $storage->url($path);
                    $product->image()->save($image);
                }
            }

            if ($request->input('categories')) {
                foreach(json_decode($request->input('categories')) as $cat) {
                    $category = Category::find($cat->value);

                    if ($category && ! in_array($category->id, $product->categories->pluck('id')->toArray())) {
                        $product->categories()->save($category);
                    }
                }
            }
        }

        return response()->json([
            'data' => $product,
            'status' => 'success',
            'message' => 'Product Added Successfully!!'
        ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($product): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($product);

        if (! $product) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid Token Input'
            ], 422);
        }

        return response()->json([
            'data' => $product,
            'status' => 'success',
            'message' => 'Product Details!!'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($product): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($product);

        if (! $product) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid Token Input'
            ], 422);
        }

        return response()->json([
            'data' => $product,
            'status' => 'success',
            'message' => 'Product Details!!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $product): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'brand_id' => 'required|integer',
            'classification_id' => 'required|integer',
            'quantity_expected' => 'required|integer',
            'categories' => 'required|array',
            'image' => 'required',
            'end_of_life' => 'required|date',
            'isDistributable' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $product = Product::find($product);

        if (! $product) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid Token Input'
            ], 422);
        }

        $product->update([
            'brand_id' => $request->input('brand_id'),
            'classification_id' => $request->input('classification_id'),
            'title' => $request->input('title'),
            'label' => Str::slug($request->input('title')),
            'code' => $request->input('code'),
            'amount' => $request->input('amount'),
            'quantity_expected' => $request->input('quantity_expected'),
            'quantity_received' => $request->input('quantity_received'),
            'description' => $request->input('description'),
            'isDistributable' => $request->input('isDistributable'),
            'end_of_life' => Carbon::parse($request->input('end_of_life')),
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            Storage::disk('public')->delete($product->image->path);

            $path = $file->store('products', 'public');
            $filename = $file->getClientOriginalName();
            $storage = Storage::disk('public')->put($filename, $path);

            if ($path) {
                $image = $product->image;
                $image->name = $filename;
                $image->size = $file->getSize();
                $image->type = $file->getClientMimeType();
                $image->path = $storage->url($path);
                $image->save();
            }
        }

        if ($request->input('categories')) {
            foreach(json_decode($request->input('categories')) as $cat) {
                $category = Category::find($cat->value);

                if ($category && ! in_array($category->id, $product->categories->pluck('id')->toArray())) {
                    $product->categories()->save($category);
                }
            }
        }

        return response()->json([
            'data' => $product,
            'status' => 'success',
            'message' => 'Product Updated Successfully!!'
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($product): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($product);

        if (! $product) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid Token Input'
            ], 422);
        }

        $old = $product;
        Storage::disk('public')->delete($product->image->path);
        $product->image()->delete();
        $product->categories()->detach();
        $product->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Product Deleted Successfully!!'
        ],200);
    }
}
