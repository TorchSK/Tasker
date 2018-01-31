<!DOCTYPE html>
<html>
    @include('includes.head')

    <body @if (isset($bodyid)) id="{{$bodyid}}" @endif>

    @include('includes.sidebar')
    @include('includes.catbar')
    @include('includes.parambar')

   	<div class="pusher">
            @include('includes.header')

		@yield('content')
		<div class="push"></div>
	</div>

	@include('includes.footer')

    </body>
</html>