<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

const ACTIVE = 1;
const SOLD = 2;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Builder[]|Collection
     */
    //те продукты которые можно купить
    public function index(Request $request)
    {
        $product_query = Product::with(['category']);
        if($request->name){
            $product_query->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->category){
            $product_query->whereHas('category',function ($query) use($request){
                $query->where('title',$request->category);
            });
        }
        if($sort = $request->input('sort')){
            $product_query->orderBy('price',$sort);
        }
        return $product_query->get();

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Product
     */
    public function store(Request $request): Product
    {
        $user = Auth::user();
        $product = new Product();
        return $product->createProduct($user,$request,$product);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Product
     */
    public function show(int $id): Product
    {
        return Product::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Product
     */
    public function update(Request $request, int $id): Product
    {
        $user = Auth::user();
        $product = Product::find($id);

        return $product->editProduct($user,$product,$request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return string
     */
    public function destroy(int $id): string
    {
        $product = Product::find($id);
        $user = Auth::user();

        if($product->status_id==SOLD){
            return 'This product was sold';
        }
        if($user->id != $product->owner_user_id){
            return 'You cant delete not yor product';
        }
        else{
            $product->delete();
            return 'Deleted';

        }
    }

    public function myProducts(Request $request){
        $user = Auth::user();
        $product_query = Product::where('owner_user_id', $user->id)->with(['category']);
        if($request->name){
            $product_query->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->category){
            $product_query->whereHas('category',function ($query) use($request){
                $query->where('title',$request->category);
            });
        }
        if($request->sort){
            $column = $request->sort;
            $direction = 'asc';
            $product_query->orderBy($column,$direction);
        }
        return $product_query->get();
    }

    public  function  buyProduct($id){
        $product = Product::find($id);
        $seller = User::find($product->seller_user_id) ;
        $user = Auth::user();

        return $product->changeOwner($product,$seller,$user);
    }
}
