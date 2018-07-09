<div class="category_image">
	<a href="/category/{{$category->url}}">
	<div class="image @if (Request::segment(1)!='admin') hover @endif">
		@if($category->image)
			<img src="/{{$category->image}}" width="180"/>
		@else
			<img src="/img/category.jpg" width="180" />
		@endif

		<div class="options">
			<a class="ui green tiny button change_cat_img_btn">Zmeň obrázok</a>
		</div>
	
	</div>

	<div class="name">
		{{$category->name}}
	</div>
	
	</a>
</div>
