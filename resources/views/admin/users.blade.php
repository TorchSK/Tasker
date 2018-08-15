@extends('layouts.admin')
@section('content')
	
	<div class="orders">
	<div class="ui horizontal divider">Uživatelia</div>
	
	<table class="ui celled selectable sortable table">
	  <thead>
	    <tr>
	    <th></th>
	    <th>ID</th>
	 	<th>Meno</th>
	    <th>Email</th>
	    <th>Admin</th>
	   	<th>VOC</th>
	   	<th>Datum registrácie</th>
	   	<th>Počet objednávok</th>

	  </tr></thead>
	  <tbody>
	  	@foreach(App\User::orderBy('created_at','desc')->get() as $user)
		<tr>
		  <td class="collapsing">
	      	<a href="{{route('admin.userDetail',['user'=>$user->id])}}" class="ui mini icon blue button"><i class="search large icon"></i></a>
	      </td>

	      <td>{{$user->id}}</td>
	   	  <td>{{$user->name}}</td>
	      <td>{{$user->email}}</td>
	      <td>{{$user->admin}}</td>
	      <td>
	      	<div class="ui checkbox admin_checkbox_onthefly" data-resource="user" data-id="{{$user->id}}" >
			  <input type="checkbox" name="voc" @if($user->voc) checked @endif>
			  <label></label>
			</div>
		  </td>
	      <td>{{Carbon\Carbon::parse($user->created_at)->format('d.m.Y H:i:s')}}</td>
	      <td>{{$user->orders->count()}}</td>



	  	</tr>

		@endforeach
	  </tbody>
	</table>
</div>
@stop