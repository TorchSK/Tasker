@extends('layouts.admin')
@section('content')

	<div class="user_detail">

		<div class="header section">

			<div class="name">
			@if ($user->name)
			{{$user->name}}
			@else
			{{$user->email}}
			@endif
			</div>
		</div>

		<div class="detail section">
		
		<div class="tabbs">

			<div class="tabs">

			    <div class="tabb ui brown button" data-tab="detail">Údaje a adresy</div>
			    <div class="tabb ui basic button" data-tab="type">Cenová zaradenie</div>
			    <div class="tabb ui basic button" data-tab="orders">Objednávky ({{$user->orders->count()}})</div>

			</div>

		  	<div class="contents">

		    	<div class="content par active" data-tab="detail">

					<div class="labeled form">
						<div class="ui header">Základné údaje</div>

						<div class="labels">
			       			<div class="item">Email *</div>
			       			<div class="item">Telefón *</div>
			       			<div class="item">Meno *</div>
			       			<div class="item">Priezvisko *</div>

						</div>

						<div class="inputs">
					      	<div class="ui large disabled input">
					            <input type="text" name="email" value="{{Auth::user()->email}}" />
					      	</div><br/>
					      	<div class="ui large input">
					            <input type="text" name="phone" value="{{Auth::user()->phone}}" />
					      	</div><br/>
					      	<div class="ui large input">
					            <input name="first_name" type="text"  value="{{Auth::user()->first_name}}"/>
					      	</div><br/>
					      	<div class="ui large input">
					            <input name="last_name" type="text"  value="{{Auth::user()->last_name}}"/>
					      	</div>

			        </div>

						</div>

						<div class="ui header">Fakturačná adresa</div>

						<div class="labeled form">

								<div class="labels">
					       			<div class="item">Ulica *</div>
					       			<div class="item">Mesto *</div>
					       			<div class="item">PSČ *</div>
								</div>

								<div class="inputs">
								
							      
							      	<div class="ui large input">
							            <input type="text" name="invoice_address_street" value="@if(Auth::user()->invoiceAddress){{json_decode(Auth::user()->invoiceAddress->address, true)['street']}}@endif" />
							      	</div><br/>
							      	<div class="ui large input">
							            <input type="text"  name="invoice_address_city" value="@if(Auth::user()->invoiceAddress){{json_decode(Auth::user()->invoiceAddress->address, true)['city']}}@endif" />
							      	</div><br/>
							      	<div class="ui large input">
							            <input type="text"  name="invoice_address_zip" value="@if(Auth::user()->invoiceAddress){{json_decode(Auth::user()->invoiceAddress->address, true)['zip']}}@endif" />
							      	</div><br/>
						

					        </div>

						</div>

						<div class="ui header">Doručovacia adresa</div>

							<div class="labeled form">

								<div class="labels">
									<div class="item">Meno a priezvisko *</div>
					       			<div class="item">Ulica *</div>
					       			<div class="item">Mesto *</div>
					       			<div class="item">PSČ *</div>
					       			<div class="item">Doplňujúce údaje</div>
					       			<div class="item">Telefón *</div>
								</div>

								<div class="inputs delivery_address">
								
							      	<div class="ui large input">
							            <input type="text" name="delivery_address_name" value="@if(Auth::user()->deliveryAddress){{Auth::user()->deliveryAddress->name}}@endif" />
							      	</div><br/>
							      	<div class="ui large input">
							            <input type="text" name="delivery_address_street" value="@if(Auth::user()->deliveryAddress){{Auth::user()->deliveryAddress->street}}@endif" />
							      	</div><br/>
							      	<div class="ui large input">
							            <input type="text" name="delivery_address_city" value="@if(Auth::user()->deliveryAddress){{Auth::user()->deliveryAddress->city}}@endif" />
							      	</div><br/>
							      	<div class="ui large input">
							            <input type="text" name="delivery_address_zip" value="@if(Auth::user()->deliveryAddress){{Auth::user()->deliveryAddress->zip}}@endif" />
							      	</div><br/>
							      	<div class="ui large input">
							            <input type="text" name="delivery_address_additional" value="@if(Auth::user()->deliveryAddress){{Auth::user()->deliveryAddress->additional}}@endif" />
							      	</div><br/>
							      	<div class="ui large input">
							            <input type="text" name="delivery_address_phone" value="@if(Auth::user()->deliveryAddress){{Auth::user()->deliveryAddress->phone}}@endif" />
							      	</div><br/>


					        </div>

						</div>
					</div>

					<div class="content" data-tab="type">

						zsazx

					</div>

					<div class="content" data-tab="orders">

						<div class="sum">Ceková suma objednávok: <number>{{$userOrdersPrice}} €</number></div>

						<table class="ui celled selectable sortable table">
						  <thead>
						    <tr>
						    <th>ID</th>
						    <th>Datum prijatia</th>
						 	<th>Meno</th>
						    <th>Suma</th>
						    <th>Stav</th>
						    <th>Dodanie</th>
						   	<th></th>

						  </tr></thead>
						  <tbody>
						  	@foreach($user->orders->sortBy('created_at') as $order)
							<tr>
						      <td>{{$order->id}}</td>
						      <td>{{Carbon\Carbon::parse($order->created_at)->format('d.m.Y H:i:s')}}</td>
						   	  <td>{{$order->invoice_name}}</td>
						      <td>{{$order->products->sum('price')}}</td>
						      <td  class="warning">{{$order->status->name}}</td>
						      <td>{{$order->delivery->name}} / {{$order->payment->name}}</td>
						      <td class="collapsing">
						      	<a href="{{route('admin.orderDetail',['order'=>$order->id])}}" class="ui mini icon blue button"><i class="search large icon"></i></a>
						      </td>

						  	</tr>

							@endforeach
						  </tbody>
						</table>


					</div>
		    		
				</div>
		    
		  </div>

		</div>
	</div>

@stop