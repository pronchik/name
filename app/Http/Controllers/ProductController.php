<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

const ACTIVE = 1;
const SOLD = 2;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //те продукты которые можно купить
    public function index()
    {
        return Product::where('status_id',1)->get();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Product::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function  create(Request $request){

        $fields = $request->validate([
            'name' => 'required|string',
            'price' => 'required|int',
            'category_id' => 'required|int'
        ]);

        $user = Auth::user();

        $product = Product::create([
            'name' => $fields['name'],
            'price' => $fields['price'],
            'category_id' => $fields['category_id'],
            'seller_user_id' => $user->id,
            'owner_user_id' => $user->id,
            'status_id' => ACTIVE
        ]);

        $response = [
            'product ' => $product ,
            'user' => $user->id
        ];

        return response($response,201);
    }

    public function myProducts(){
        $user = Auth::user();
        return  Product::where('owner_user_id', $user->id)->get();
    }

    public  function  buyProduct($id){
        $product = Product::find($id);
        $seller = User::find($product->seller_user_id) ;
        $user = Auth::user();

        if($user->id == $product->owner_user_id){
            return 'You cant buy your product';
        }
        elseif($product->status_id == SOLD){
            return 'This product is sold';
        }
        elseif ($user->balance < $product->price) {
            return 'Not enough money';
        }
        else{
            $product->owner_user_id = $user->id;
            $product->buyer_user_id = $user->id;
            $product->status_id = SOLD;
            $user->balance -= $product->price;
            $seller->balance += $product->price;

            $user->save();
            $seller->save();
            $product->save();
        }
        return $product;
    }
}
