<div class="ui fluid steps unstackable">
  <a class="step cart_payment {{$context}} @if ($cart['payment_method']==$payment->id) completed active @endif @if ($cart['delivery_method'] && !in_array($cart['delivery_method'], $payment->deliveryMethods->pluck('id')->toArray())) disabled @endif" data-id="{{$payment->id}}" data-type="payment" data-payment_method="{{$payment->id}}" data-delivery_methods="{{$payment->deliveryMethods->pluck('id')}}" @if($cart['price'] < App\Setting::whereName('min_free_shipping_price')->first()->value || App\Setting::whereName('min_free_shipping_price')->first()->value==0) data-price="{{$payment->price}}" @else data-price="0" @endif data-note="{{$payment->note}}" data-name="{{$payment->name}}" data-price="{{$payment->price}}" data-icon="{{$payment->icon}}" data-desc="{{$payment->desc}}">
    <i class="{{$payment->icon}} icon"></i>
    <div class="content">
      <div class="title">{{$payment->name}}</div>
      <div class="description">{{$payment->desc}}</div>
    </div>

   	<div class="price">@if(App\Setting::whereName('min_free_shipping_price')->first()->value==0 || $cart['price'] < App\Setting::whereName('min_free_shipping_price')->first()->value){{$payment->price}} @else 0 @endif &euro;</div>
  
	  @if($context=='admin')
	  	<div class="options">
	  		<icons>
	  			<i class="big teal edit icon"></i>
	  			<i class="big red delete icon"></i>
	  		</icons>
	  	</div>
	  @endif

  </a>
</div>