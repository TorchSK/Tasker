@extends('layouts.admin')
@section('content')

          <div id="admin_products_wrapper" class="admin_wrapper">


            @if ($category!='unknown')

            <div class="ui horizontal divider active title">URL kategórie</div>

              <div class="ui labeled input" id="edit_product_name_input">
                 <div class="ui label">Názov</div>
                 <input type="text" value="{{$category->name}}" />
            </div>

              <div class="ui labeled input" id="edit_product_url_input">
                <div class="ui label">URL</div>
                 <input type="text" value="{{$category->url}}" />
            </div>
            <br />
            
            <a class="ui green button" id="edit_category_submit" data-categoryid="{{$category->id}}">Ulož</a>

            <div class="ui horizontal divider active title">Obrázok kategórie</div>
 
            <div id="category_image_div" data-categoryid="{{$category->id}}">

              <div>@include('categories.image')</div>

              <form action="/category/image/upload" class="dropzone" id="category_image_dropzone"> 
                  <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
              </form>
              <div class="crop_preview"></div>
              <div><div class="crop_ok ui green button" data-categoryid="{{$category->id}}">OK</div></div>
            </div>

            <div class="ui horizontal divider active title">Podkategórie</div>

            <div class="subcategories" id="category_subcat_div">
              @foreach($category->children->sortBy('order') as $child)
                @include('categories.image',['category'=>$child])
              @endforeach 

              <div class="add_category_btn">
                <i class="big icons">
                  <i class="plus brown icon"></i>
                </i>
                <div class="text">Pridaj subkategóriu</div>
              </div>
            </div>


            <div class="ui horizontal divider active title">Parametre / Filtre</div>

            <div class="admin_filters">

              <div class="active">
              <select class="ui fluid search dropdown" multiple="" id="admin_category_params_selection">
              @foreach (App\Parameter::all() as $param)
                <option value="{{$param->id}}" @if(App\Category::find($category->id)->parameters->contains($param->id)) selected @endif>{{$param->display_key}}</option>
              @endforeach
              </select>
            </div>  

            
            </div>
            @endif


            <div class="ui horizontal divider active title">Produkty</div>

            <div id="grid">
            
            @if ($category!='unknown')
				      <a class="item grid new_product_btn" href="/product/create?category={{$category->id}}">
    					   <i class="huge icons">
    					  <i class="plus brown icon"></i>
    					</i>
    					Pridaj produkt
    				</a>
        @endif

    		@foreach ($products as $product)
    			@include('products.row')
    		@endforeach
           </div>

 
         </div>

@stop