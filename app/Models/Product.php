<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use const App\Http\Controllers\ACTIVE;
use const App\Http\Controllers\SOLD;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'price',
        'category_id',
        'seller_user_id',
        'owner_user_id',
        'status_id'
    ];

    protected $hidden = [
        'updated_at',
        'created_at'
    ];

    protected $allowedSorts=[
        'status_id',
        'price'
    ];

    public function seller_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function buyer_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function owner_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function changeOwner($product,$seller,$user)
    {
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

        return $product->load(['category:id,title']);
    }

    public function editProduct($user,$product,$request){
        $request->validate([
            'name' => 'string',
            'price' => 'int',
            'category_id' => 'int'
        ]);
        if($product->status_id==SOLD){
            return 'This product was sold';
        }
        if($user->id != $product->owner_user_id){
            return 'You cant update not yor product';
        }
        else{
            if($request->name){
                $product->name = $request->name;
            }
            if($request->price){
                $product->price = $request->price;
            }
            if($request->category_id){
                $product->category_id = $request->category_id;
            }
            $product->save();
        }
        return response([$user,$product,$request]);
    }

    public function createProduct($user,$request,$product){
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|int',
            'category_id' => 'required|int'
        ]);
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->seller_user_id = $user->id;
        $product->owner_user_id = $user->id;
        $product->status_id = ACTIVE;

        $product->save();
        return $product;
    }

    public function getMyProducts($request,$product){
        if($request->name){
            $product->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->category){
            $product->whereHas('category',function ($query) use($request){
                $query->where('title',$request->category);
            });
        }
        if($sort = $request->input('sort')){
            $product->orderBy('price',$sort);
        }
        return $product->get();
    }
}
