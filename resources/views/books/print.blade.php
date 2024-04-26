@php($pages ??= [])
@php($height ??= 300)
@php($ratio ??= 2)
<html lang="he" dir="rtl">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body, * { font-family: 'DejaVu Sans', sans-serif; direction: rtl;language: 'hebrew'}

        @media print {
            @page {
                size: landscape;  /* auto is the initial value */
                margin: 0;  /* this affects the margin in the whole document */
            }

            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body class="landscape" dir="rtl" style="padding: 0;">
    <?php
    /** @var \App\Models\Book $book */
    $data = $book->toBookArray();

    $frontCover = array_shift($data);
    $backCover = array_pop($data);
    $splitPages = array_chunk($data, 2);
    ?>

    <div style="padding: 0; height: 100%">
        <div style="overflow: hidden; background-size: contain;   	background-position: center;
                            height: 100%;background-image: url('{{$frontCover["image"]}}');">
            <div style="overflow: hidden; background-size: contain;   background-repeat: no-repeat; 	background-position: center;  backdrop-filter: blur(16px);
                                height: 100%;background-image: url('{{$frontCover["image"]}}');">
                <div><h3 style="text-align: center; font-size: 3rem; line-height: 1.25;
                                direction: rtl; -webkit-text-stroke-color: #FFFFFF; -webkit-text-stroke-width: 1px">{{ $frontCover["title"] }}</h3>
                </div>
            </div>
        </div>
    </div>

    @foreach($splitPages as $i => $pages)
        <div style="padding: 0; height: 100%">
            @foreach($pages as $ii => $page)
            <div style="float: @if(!$ii)right @else left @endif; width: @if($page["image"] ?? null) 65% @else 35% @endif; height: 100%; margin: 0;">
            @if($page["image"] ?? null)
                <div style=" overflow: hidden; background-size: cover;   background-repeat: no-repeat;
                            height: 100%;padding: 30px;background-image: url('{{$page["image"]}}');">
            @endif
            @if($page["title"] ?? null)
                        <div><h3 style="text-align: center; font-size: 3rem; line-height: 1.25;
                            direction: rtl; -webkit-text-stroke-color: #FFFFFF; -webkit-text-stroke-width: 1px">{{ $page["title"] }}</h3>
                        </div>
            @endif
                    <div style="font-size: 1.5rem; line-height: 2rem; _height: 100%; display: flex; flex-direction: column; gap: 0.5rem; justify-content: center; color: black;
                                direction: rtl; padding: 30px 30px 0 30px; height: 460px">
            @foreach(explode("\n", $page["content"] ?? "") as $p)
                            <p @if($page["image"] ?? null)style="-webkit-text-stroke-color: #FFFFFF; -webkit-text-stroke-width: thin;font-size: 2.25rem; line-height: 2.5rem;" @endif>{{ $p }}</p>
            @endforeach
                    </div>
            @if($page["pageNum"] ?? null)
                        <div style="text-align: center; color: #9ca3af;">-{{$page["pageNum"]}}-</div>
            @endif
            @if($page["image"] ?? null)</div>@endif
            </div>
            @endforeach
        </div>

    @endforeach


    <div style="padding: 0; height: 100%">
        <div style="overflow: hidden; background-size: contain;   	background-position: center;
                            height: 100%;background-image: url('{{$backCover["image"]}}');">
            <div style="overflow: hidden; background-size: contain;   background-repeat: no-repeat; 	background-position: center;  backdrop-filter: blur(16px);
                                height: 100%;background-image: url('{{$backCover["image"]}}');">
                <div><h3 style="text-align: center; font-size: 3rem; line-height: 1.25;
                                direction: rtl; -webkit-text-stroke-color: #FFFFFF; -webkit-text-stroke-width: 1px">{{ $backCover["title"] }}</h3>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    setTimeout(() => {window.print();alert("נא ללחוץ על 'הדפס' בדפדפן או להקיש ctrl + P");}, 500)

</script>
</html>
