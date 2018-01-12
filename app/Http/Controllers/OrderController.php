<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;
use App\Product;

use Cookie;
use Auth;
use Mail;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function store()
    {
    	$orderData = Cookie::get('cart');

    	$order = new Order();

    	if (Auth::check())
    	{
    		$order->user_id = Auth::user()->id;
    	}

    	$order->status_id = 0;


    	$order->delivery_method = $orderData['delivery'];
    	$order->payment_method = $orderData['payment'];


    	$order->invoice_name = $orderData['invoiceAddress']['name'];
	   	$order->invoice_phone = $orderData['invoiceAddress']['phone'];
		$order->invoice_email = $orderData['invoiceAddress']['email'];
		$order->invoice_address_street = $orderData['invoiceAddress']['street'];
		$order->invoice_address_zip = $orderData['invoiceAddress']['zip'];
		$order->invoice_address_city = $orderData['invoiceAddress']['city'];

		if ($orderData['deliveryAddress']['street'])
		{
			$order->delivery_address_name = $orderData['deliveryAddress']['name'];
			$order->delivery_address_street = $orderData['deliveryAddress']['street'];
			$order->delivery_address_zip = $orderData['deliveryAddress']['zip'];
			$order->delivery_address_city = $orderData['deliveryAddress']['city'];
			$order->delivery_address_phone = $orderData['deliveryAddress']['phone'];
		}
		else
		{
			$order->delivery_address_name = $orderData['invoiceAddress']['name'];
			$order->delivery_address_street = $orderData['invoiceAddress']['street'];
			$order->delivery_address_zip = $orderData['invoiceAddress']['zip'];
			$order->delivery_address_city = $orderData['invoiceAddress']['city'];
			$order->delivery_address_phone = $orderData['invoiceAddress']['phone'];
		}
		$order->invoice_first_additional = '';

        $price = 0;
		foreach ($orderData['items'] as $productid)
		{
			$product = Product::find($productid);
            $price = $price + $product->price;
			$order->products()->attach($product);
		};

        $order->price = $price;
        $order->save();

        $user = Auth::user();
        Mail::to($order->invoice_email)->queue(new NewOrder($order));

    }

    public function success()
    {
    	return view('orders.success');
    }

    public function myhistory()
    {
    	return view('orders.myhistory');
    }
}
