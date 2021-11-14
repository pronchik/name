<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return Product::where('id', $id)->get();
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

        $userId = Auth::user()->id;

        $product = Product::create([
            'name' => $fields['name'],
            'price' => $fields['price'],
            'category_id' => $fields['category_id'],
            'seller_user_id' => $userId,
            'owner_user_id' => $userId,
            'status_id' => 1
        ]);

        $response = [
            'product ' => $product ,
            'user' => $userId
        ];

        return response($response,201);
    }

    public function myProducts(){
        $userId = Auth::user()->id;
        return  Product::where('owner_user_id', $userId)->get();
    }

    public  function  buyProduct($id){
        $product = Product::where('id',$id)->get();
        $userId = Auth::user()->id;
        $sellerUserId = $product->first()->seller_user_id;
        $sellerUserIdBalance = User::where('id',$sellerUserId)->get()->first()->balance;
        $productOwnerId = $product->first()->owner_user_id;
        $productPrice = $product->first()->price;
        $userBalance = User::where('id',$userId)->get()->first()->balance;

        if($productPrice <= $userBalance && $product->first()->status_id == 1){
            if($userId != $productOwnerId){
                Product::where('id', $id)->update(array('owner_user_id' => $userId));
                Product::where('id', $id)->update(array('seller_user_id' => $userId));
                Product::where('id', $id)->update(array('status_id' => 2));
                User::where('id', $userId)->update(array('balance' => $userBalance-$productPrice));
                User::where('id', $sellerUserId)->update(array('balance' => $sellerUserIdBalance+$productPrice));
            }
            else{
                $response = [
                    'error'=>'You cant buy your product'
                ];
                return response($response,201);
            }
            return  Product::where('id',$id)->get();
        }
        $response = [
            'error'=>'Not enough money or product sold'
        ];

        return response($response,201);

    }
}
