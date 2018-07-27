<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>日志查看</title>
    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container-fluid">
    @foreach($list as $item)
    <div class="row">
        <div class="col-md-1">
            <p>
                @php dump($item->id) @endphp
            </p>
        </div>
        <div class="col-md-2">
            <p>
                @php dump($item->url) @endphp
            </p>
        </div>
        <div class="col-md-3">
            <p>
                @php dump(json_decode($item->body,true)) @endphp
            </p>
        </div>
        <div class="col-md-3">
            <p>
                @php dump(json_decode($item->sql,true)) @endphp
            </p>
        </div>
        <div class="span3">
            <p>
                @php dump(json_decode($item->response_body,true)) @endphp
            </p>
        </div>
    </div>
    @endforeach
</div>
{{ $list->links() }}
</body>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</html>