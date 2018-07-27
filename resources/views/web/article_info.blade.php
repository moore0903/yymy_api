<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="build" content="" />
    <meta name="copyright" content="" />
    <meta name="Reply-to" content="" />
    <meta name="robots" content="all" />
    <link rel="start" href="" title="Home" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="HandheldFriendly" content="true" />
    <meta http-equiv="x-rim-auto-match" content="none" />
    <meta name="format-detection" content="telephone=no">
    <meta name="description" content="" />
    <meta name="keywords" content="茁伴--{{ $article->title }}" />
    <meta name="author" content="茁伴--{{ $article->title }}" />
    <meta name="copyright" content="茁伴--{{ $article->title }}" />
    <title>茁伴--{{ $article->title }}</title>
    <link href="{{ asset('web/theme/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('web/theme/css/flexslider.css') }}" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="{{ asset('web/theme/common/flexible.js') }}"></script>
    <script type="text/javascript" src="{{ asset('web/theme/common/jquery-1.7.2.min.js') }}"></script>
    {{--<script type="text/javascript" src="{{ asset('web/theme/common/tab.js') }}"></script>--}}
    <script type="text/javascript">
        $(function(){
            $(".anniuhide").click(function(){
                $(this).parents().find(".detailbot").hide();
            })
        })
    </script>
</head>
<body>
<div class="wrap fmyh">
    <div class="detailcont">
        <div class="section">
            <div class="title">
                <h3>{{ $article->title }}</h3>
                <div class="time clear">
                    <p class="name fl">{{ $article->catalog->title }}</p>
                    <p class="nr fl">{{ date('Y-m-d',strtotime($article->created_at)) }}</p>
                </div>
            </div>
            <div class="detail">
                {!! $article->content !!}
            </div>
        </div>
    </div>
</div>
</body>
</html>