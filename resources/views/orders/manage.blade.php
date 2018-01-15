@extends('layouts.master')
@section('content')

<div class="pad dark wrapper ct">
<div class="order_history container ct">
	

	<div class="orders">
	<div class="ui horizontal divider">Otvorené objednávky</div>

	@foreach($orders as $order)
		<div class="item">
			<div class="id">{{$order->id}}</div>
			<div class="date">{{Carbon\Carbon::parse($order->created_at)->day.'.'.Carbon\Carbon::parse($order->created_at)->month.'.'.Carbon\Carbon::parse($order->created_at)->year}}</div>
			<div class="price">{{$order->price}} Eur</div>
			<div class="detail">{{$order->status->name}}</div>

		</div>
	@endforeach
	</div>

</div>
</div>
@stop