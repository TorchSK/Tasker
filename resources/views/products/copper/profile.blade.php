@extends('layouts.master')
@section('content')

<div class="main wrapper" id="product">

<div class="flex_row">

@include('includes/filterbar', ['sticky'=> true])

<div id="product_wrapper">
<div id="product_main_wrapper" class="wrapper @if($product->active==0) inactive @endif" data-id="{{$product->id}}" data-gallery="{{$product->code}}" data-index="0">
  <div class="container flex_row">
      
    <div class="images">



        <div class="img">
           @if ($product->images->count() == 0)
           <img src="/img/empty.jpg" class="ui image" />
           @elseif ($product->image)
           <img src="/{{$product->image->path}}" class="ui image" />
           @endif
        </div>
        
          <div class="other_img">
          @if ($product->images->count() > 1)

          @foreach($product->images as $image)
           <img src="/{{$image->path}}" class="ui image" width="200px" />
           @endforeach
           @endif

          @if ($product->videos->count() > 0)

           @foreach($product->videos as $video)
           <div class="pretty-embed" data-pe-videoid="{{explode('v=',$video->path)[1]}}" data-pe-fitvids="true"></div>

           @endforeach
           @endif


        </div>


    </div>

    <div class="info">
    	
    	<div id="name">{{$product->name}}</div>

		<div id="code">{{$product->code}} </div>
      @if ($product->sale)
    <div id="sale" class="ui green large label"><i class="money icon"></i> Zľava</div>
    @endif

    @if ($product->new)
    <div id="new" class="ui blue large label"><i class="star icon"></i> Novinka</div>
    @endif

   		<div class="ui header" id="product_categories">
        <div>
   			@foreach ($product->categories as $category)
        <div>
        @if(isset($category->parent) && $category->parent->count() > 0  && isset($category->parent->parent) && $category->parent->has('parent'))
        <a href="{{route('category.products',['path'=> $category->parent->parent->full_url])}}" class="effect-1">{{$category->parent->parent->name}}</a> - 
        @endif
        @if(isset($category->parent) && $category->parent->count() > 0)
        <a href="{{route('category.products',['path'=> $category->parent->full_url])}}" class="effect-1">{{$category->parent->name}}</a> - 
        @endif
        <a href="{{route('category.products',['path'=> $category->full_url])}}" class="effect-1">{{$category->name}}</a>
        </div>
   			@endforeach
        </div>

        <div class="" id="maker">Výrobca: <b><a href="/m/{{$product->maker}}" class="effect-1">{{$product->maker}}</a></b></div>

   		</div>

		<div class="ui divider"></div>
    <div id="desc">
    {{$product->desc}}
    </div>

    <div class="ui divider"></div>

    <div id="prices">
      <div id="price_type_info">
      @if(Auth::check() && Auth::user()->voc)
      Všetky ceny sú veľkoobchodné
      @else
      Všetky ceny sú maloobchodné
      @endif
      </div>
      <table class="ui unstackable table">
        <thead>
          <tr>
            <th>
              @if($product->price_unit=='m')
              Minimálny počet metrov
              @else
              Minimálny počet kusov
              @endif
              </th>
            <th>Cena za {{$product->price_unit}}</th>
  
          </tr>
        </thead>
        <tbody id="product_price_thresholds">
          @foreach($product->priceLevels as $key=>$priceLevel)
          <tr @if($key==0) class="positive" @endif>
            <td class="threshold" data-value="{{$priceLevel->threshold}}">{{$priceLevel->threshold}}</td>
            <td>
              @if(Auth::check() && Auth::user()->voc)
                @if($product->sale)
                <div id="price" class="crossed">{{$priceLevel->voc_regular}} &euro; </div>
                <div id="final_price">{{$priceLevel->voc_sale}} &euro; </div>
                <div class="without_vac">{{round($priceLevel->voc_sale/1.2,2)}} &euro; bez dph</div>
                @else
                <div id="final_price">{{$priceLevel->voc_regular}} &euro;</div>
                <div class="without_vac">{{round($priceLevel->voc_regular/1.2,2)}} &euro; bez dph</div>
                @endif
              @else
                @if($product->sale)
                <div id="price" class="crossed">{{$priceLevel->moc_regular}} &euro; </div>
                <div id="final_price">{{$priceLevel->moc_sale}} &euro;</div>
                <div class="without_vac">{{round($priceLevel->moc_sale/1.2,2)}} &euro; bez dph</div>

                @else
                <div id="final_price">{{$priceLevel->moc_regular}} &euro;</div>
                <div class="without_vac">{{round($priceLevel->moc_regular/1.2,2)}} &euro; bez dph</div>
                @endif
              @endif
            </td>
          </tr>
          @endforeach

        </tbody>
      </table>
      </div>

  <div class="stock @if($product->stock >0) instock @else outstock @endif">
    @if($product->stock > 0)
    Skladom     {{$product->stock}} {{$product->price_unit}}
    @else
    Na objednávku 
    @endif
  </div>


      <div id="product_buy_qty_m_slider" data-min="{{$product->priceLevels->min('threshold')}}" data-max="500" data-step="{{$product->step}}"></div>
      <div id="product_buy_qty_value">Kupujete: <qty>{{$product->priceLevels->min('threshold')}}</qty> {{$product->price_unit}} za 
        <price>
          @if(Auth::check() && Auth::user()->voc)
            @if($product->sale)
            {{$product->priceLevels->min('threshold')*$product->priceLevels->where('threshold',$product->priceLevels->min('threshold'))->first()->voc_sale}}
            @else
            {{$product->priceLevels->min('threshold')*$product->priceLevels->where('threshold',$product->priceLevels->min('threshold'))->first()->voc_regular}}
            @endif
          @else
            @if($product->sale)
            {{$product->priceLevels->min('threshold')*$product->priceLevels->where('threshold',$product->priceLevels->min('threshold'))->first()->moc_sale}}
            @else
            {{$product->priceLevels->min('threshold')*$product->priceLevels->where('threshold',$product->priceLevels->min('threshold'))->first()->moc_regular}}
            @endif
          @endif
        </price> &euro;</div>

    <div id="product_detail_tocart_btn" class="ui large brown labeled icon button" data-qty="{{$product->priceLevels->min('threshold')}}"><i class="add to cart icon"></i>Kúpiť</div>


 </div>
</div>
</div>


<div id="product_params_wrapper" class="wrapper">
  <div class="container">

      @if ($product->parameters->count() > 0)

      <table class="ui celled unstackable table">
       <tbody>
          @foreach ($product->parameters as $parameter)
              <tr>
                <td class="collapsing"><b>{{$parameter->definition->display_key}}</b></td>
                <td> {{$parameter->value}}</td>
              </tr>
          @endforeach
        @else
          Žiadne parametre
        @endif
          </tbody>
    </table>

@foreach($product->files as $file)
<a href={{ asset($file->path) }} target="_blank"><i class="icon huge brown file pdf outline" ></i> Katalógový list</a>
@endforeach

</div>
</div>

@if($product->relatedProducts->count() > 0)
<div class="pad wrapper ct" id="product_detail_suggested_wrapper">

  <div class="container">
    <div class="ui header">Doporučené výrobky</div>

      <div id="grid">

      @foreach($product->relatedProducts as $relprod)
        @include('products.row',['product'=>$relprod])
      @endforeach

      </div>
</div>
</div>
@endif

<div class="pad wrapper ct">
               <div class="ui horizontal divider">Hodnotenia</div>

      <div class="container ct">


        <div class="overall_rating">
          <div class="rating_number"><number>@if($product->ratings->pluck('value')->avg() > 0) {{$product->ratings->pluck('value')->avg()}}</number> @else 0 @endif <span>({{$product->ratings->count()}} hodnotení)</span></div>
          <div class="disabled rating" @if($product->ratings->count()>0) data-rating="{{$product->ratings->pluck('value')->avg()}}" @else data-rating="0" @endif">
          </div>
        </div>

        @if(Auth::check())
        <div class="my_rating">
          <div class="rating_number"><number>@if(App\Rating::where('user_id',Auth::user()->id)->where('ratingable_id', $product->id)->count() >0) {{App\Rating::where('user_id',Auth::user()->id)->where('ratingable_id', $product->id)->first()->value}}</number> @else 0 @endif <span>(Moje hodnotenie)</span></div>

           <div class="my rating" @if(App\Rating::where('user_id',Auth::user()->id)->where('ratingable_id', $product->id)->count() >0) data-rating="{{App\Rating::where('user_id',Auth::user()->id)->where('ratingable_id', $product->id)->first()->value}}" @else data-rating="0" @endif>
          </div>
        </div>
        @else
          <div class="my_rating"><a class="ui teal button" href="/login">Prihláste sa</a></div>

        @endif

        <div class="ratings_list">
          @foreach($product->ratings as $rating)
            <div class="rating_div">
              <div class="user">
                @if ($rating->user->first_name)
                {{$rating->user->first_name}} {{$rating->user->last_name}} 
                @else
                  Zákazník
                @endif

                <div class="text"> {{$rating->text}}</div>

              </div>

              <div class="value">

                  <div class="disabled rating" data-rating="{{$rating->value}}">
                    </div>

              </div>
            </div>
          @endforeach
        </div>
  </div>
  @if(Auth::check())
  <div id="myrating" @if(App\Rating::where('user_id',Auth::user()->id)->where('ratingable_id', $product->id)->count() >0) data-rating="{{App\Rating::where('user_id',Auth::user()->id)->where('ratingable_id', $product->id)->first()->value}}" @else data-rating="0" @endif></div>
  @endif

</div>
</div>
</div>
</div>

@stop