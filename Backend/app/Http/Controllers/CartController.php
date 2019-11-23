<?php

namespace App\Http\Controllers;

use App\cook;
use App\Http\Resources\mainCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class CartController extends Controller
{
    public function index() {
        $session = session()->getid();
        $carts = \Cart::session($session)->getContent();
        $cart_array = [];
        if ($carts) {
            $cartsArray = json_decode($carts);
            foreach($cartsArray as &$cart){
                $cart = (array) $cart;
                $cart_array[] = $cart;
            }
        }

        $collection = $this->paginate($cart_array, $perPage = 6, $page = null, $options = []);
        return new mainCollection($collection);
    }

    public function add_cart(Request $request) {
        $session = session()->getId();
        $product = cook::find($request->id);
        $attributes = array('image_path' => $product->image_path);
        \Cart::session($session)->add(
            $product->id,
            $product->title,
            $product->price,
            $request->qty,
            $attributes
        );

        $cart = \Cart::session($session)->getContent();
        return response()->json([
           'success' => $cart
        ]);
    }

    public function destroy($id) {
        $session = session()->getId();
        \cart::session($session)->remove($id);
        return response()->json([
           'success' => true
        ]);
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
