<div id="filterbar" class="horizontal">
	<div class="container">
    @foreach(App\Category::orderBy('order')->get()->toTree() as $category)

    	<a href="{{route('category.products',['category'=>$category->full_url])}}" class="item @if(isset($requestCategory) && ($category->id == $requestCategory->id || in_array($category->id,$requestCategory->ancestors->pluck('id')->toArray()))) active @endif" data-cat="{{$category->id}}">{{$category->name}}</a>

    	<div class="ui fluid basic popup bottom left transition hidden" id="cat_popup_{{$category->id}}">

    		<div class="subcats">
			@foreach($category->children as $child)
				<div class="sub1">
					<a href="{{route('category.products',['category'=>$child->full_url])}}" class="name">{{$child->name}}</a>
							@foreach($child->children as $child2)
								<div class="sub2">
									<a href="{{route('category.products',['category'=>$child2->full_url])}}" class="name">{{$child2->name}}</a>
								</div>
							@endforeach	
				</div>
			@endforeach	
			</div>

		</div>

	@endforeach

</div>
</div>
