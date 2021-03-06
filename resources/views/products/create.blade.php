@extends('layouts.admin')
@section('content')

<form action="/product" method="POST" id="create_product_form" class="admin_wrapper">

<div id="product_options" class="ct">
 <div class="container ct">
		<button type="submit" class="ui huge brown button" id="create_product_submit">Vytvor produkt</button>

 </div>
</div>

<div id="product_main_wrapper" class="product_detail flex_row">
    <div class="images">
        <div class="img">
           <div action="/file/upload" class="dropzone" id="product_detail_dropzone"> 
           	<input name="_token" hidden value="{!! csrf_token() !!}" />
           	<div class="dz-message">Klikni pre nahranie súboru</div>
           </div>
        </div>


        <div class="ui grid three column">

        	<div class="column">
	       	<div id="create_product_new_flag">
			<div class="ui checkbox">
			  <input type="checkbox" name="new">
			  <label>Novinka</label>
			</div>
			</div>
			</div>

        	<div class="column">
			<div id="create_product_sale_flag">
			<div class="ui checkbox">
			  <input type="checkbox" name="sale">
			  <label>V zľave</label>
			</div>
			</div>
			</div>
			
			<div class="column">

			    <div class="ui selection dropdown" id="edit_product_unit_input">
			      <input type="hidden" name="unit" value="m">
			      <i class="dropdown icon"></i>
			      <div class="text">m</div>
			      <div class="menu">
			        <div class="item" data-value="m">m</div>
			        <div class="item" data-value="ks">ks</div>
			      </div>
			    </div>
				</div>
			</div>

       	

		<div id="product_price_levels_list">
			@include('products.pricelevel')
		</div>

   		<div class="ui teal button" id="add_price_level_btn">Pridaj cenovú úroveň</div>



			
		</div>


    <div class="info">
    	
    	@if($errors->any())
		<div class="ui negative message">{{$errors->first()}}</div>
		@endif

    	<div class="ui header">Názov</div>

        <div class="ui fluid input">
           <input type="text" name="name" placeholder="Názov produktu" @if(Request::has('duplicate'))value="{{App\Product::find(Request::get('duplicate'))->name}}@endif" />
   		</div>

		<div class="ui header">Kód produktu</div>

        <div class="ui fluid input">
           <input type="text" name="code" placeholder="Kód produktu" @if(Request::has('duplicate'))value="{{App\Product::find(Request::get('duplicate'))->code}}@endif" />
   		</div>

		<div class="ui header">Výrobca</div>

        <div class="ui fluid input" id="create_product_maker_input">
           <input type="text" name="maker" placeholder="Výrobca" @if(Request::has('duplicate'))value="{{App\Product::find(Request::get('duplicate'))->maker}}@endif"/>
   		</div>


   		<div class="ui header">Kategória</div>
		<select multiple="" name="categories" id="create_product_categories_input" class="ui fluid normal dropdown">
		<option value="">Kategória</option>
		@foreach (App\Category::all() as $category)
		<option @if((isset($selectedCategory) && $selectedCategory->id==$category->id) || (Request::has('duplicate')) && in_array($category->id, App\Product::find(Request::get('duplicate'))->categories()->pluck('id')->toArray())) selected @endif value="{{$category->id}}">{{$category->name}}</option>
		@endforeach
		</select>

		<div class="ui header">Popis</div>

		<div class="ui form">
		  <div class="field">
		    <textarea name="desc">@if(Request::has('duplicate')){{App\Product::find(Request::get('duplicate'))->desc}}@endif</textarea>
		  </div>
		</div>

		<div class="ui header">Parametre</div>

		<div id="create_product_params">
			<div class="row">
				<div class="ui search selection dropdown">
				  <input type="hidden" name="key[]">
				  <i class="dropdown icon"></i>
				    <div class="default text">Parameter</div>

				  <div class="menu">
				  	@if(Request::has('duplicate'))
	 					@foreach (App\Product::find(Request::get('duplicate'))->categories()->first()->parameters as $param)
					  		@include('products.paramoptions')
					  	@endforeach
				  	@elseif(Request::has('category'))
					  	@foreach (App\Category::find(Request::get('category'))->parameters as $param)
					  		@include('products.paramoptions')
					  	@endforeach
				  	@endif
				  </div>
				</div>
				<div class="ui input value"><input type="text" name="value[]" /></div>
			</div>
		</div>

		<div class="ui teal button" id="create_product_add_param_row">Pridaj</div>

 </div>
</div>

</form>

@stop