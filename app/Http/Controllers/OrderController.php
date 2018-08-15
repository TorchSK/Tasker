<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;
use App\Product;
use App\Mail\NewOrder;

use App\Services\Contracts\CartServiceContract;
use App\Services\Contracts\ProductServiceContract;

use DB;
use Cookie;
use Auth;
use Mail;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        CartServiceContract $cartService,
        ProductsServiceContract $productService
    )
    {
        $this->cartService = $cartService;
        $this->productService = $productService;

    }

    public function store()
    {   
        if (Auth::check())
        {
            $orderData = Auth::user()->cart;
            $orderData['items'] =  Auth::user()->cart->products->pluck('id');
        }
        else
        {
            $orderData = Cookie::get('cart');
        }

    	$order = new Order();

    	if (Auth::check())
    	{
    		$order->user_id = Auth::user()->id;
    	}

    	$order->status_id = 0;


    	$order->delivery_method_id = $orderData['delivery_method'];
    	$order->payment_method_id = $orderData['payment_method'];
        $order->price = $orderData['price'];


    	$order->invoice_address = $orderData['invoice_address'];


		if ($orderData['delivery_address_flag'])
		{
			$order->delivery_address = $orderData['delivery_address'];
		}

        $order->save();

        foreach($orderData['items'] as $key => $productid)
        {
            $order->products()->attach($productid, ['price' => ]);
        }
    

        $user = Auth::user();
        Mail::to(json_decode($order->invoice_address)->email)->queue(new NewOrder($order));

        //delete the cart
        //$this->cartService->delete();  


    }

    public function success()
    {
    	return view('orders.success');
    }

    public function myhistory()
    {
    	return view('orders.myhistory');
    }

    public function countByDays($daysago)
    {
        $orders = Order::select(DB::raw('DATE_FORMAT(created_at, "%Y-%c-%e") as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        return $orders;
    }

}
