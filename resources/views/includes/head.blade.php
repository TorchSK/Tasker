<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="CY9IwgJZGjaR_icVxrolcCKFZUjWhHfx9QDnJ32_MLE" />
    
    @if($appname=='dedra')

        @if(isset($description))
            <meta name="description" content="{{$description}}">
        @else
            <meta name="description" content="Dedra eko čistiace prostriedky, darčeky pre mužov, darčeky pre ženy, šperky, drogéria pre domácnosť">
        @endif

        @if(isset($keywords))
            <meta name="keywords" content="{{$keywords}}">
        @else
            <meta name="keywords" content="dedra,darček,darčeky pre muža,darčeky pre ženu,drogeria,dekoracie,šperky,doplnky do domácnosti,do bytu,domov,stolovanie,porcelán,bižutéria,cestovanie,keramika">
        @endif

    @endif

    <meta name="robots" content="index, follow">
    
    <meta property="fb:app_id" content="" />
    <meta property="og:url" content="{{Request::url()}}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@if(isset($title)){{$title}} @else Dedraslovakia.sk @endif"/>
    <meta property="og:description" content="@if(isset($description)){{substr($description,0,100)}}@else Dedra eko čistiace prostriedky,darčeky pre mužov,darčeky pre ženy,šperky,drogéria pre domácnosť @endif" />
    @if (isset($ogImages))
        @foreach($ogImages as $image)
            <meta property="og:image" content="{{$image}}" />
        @endforeach
    @else
    <meta property="og:image" content="{{url('img/'.$appname)}}_favico.png" />
    @endif

    <title>
    @if(isset($title))
    {{$title}} | {{App\Setting::firstOrCreate(['name'=>'home_title'])->value}}.sk
    @else
    {{App\Setting::firstOrCreate(['name'=>'home_title'])->value}}.sk
    @endif
    </title>

    <link rel="icon" type="image/png" href="{{url('img/'.$appname)}}_favico.png" />

    <link rel="canonical" href="https://dedraslovakia.sk/@if(Request::path()!="/"){{Request::path()}}@endif">


    <link media="all" type="text/css" rel="stylesheet" href="/css/ext.css">


    <link media="all" type="text/css" rel="stylesheet" href="/css/{{$appname}}.css">



    @if(Auth::check())
    <script>
        window.Laravel = {!! json_encode([
            'user' => Auth::user()
        ]) !!};
    </script>
    @else
    <script>
        window.Laravel = {!! json_encode([
            'user' => ['id'=>Session::getId()]
        ]) !!};
    </script>
    @endif


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-26854117-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-26854117-1');
    </script>


</head>