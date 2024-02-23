<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/js/turnjs4/all.js"></script>
    <script type="text/javascript" src="/js/turnjs4/hash.js"></script>
    <script type="text/javascript" src="/js/turnjs4/turn.min.js"></script>
    <script type="text/javascript" src="/js/turnjs4/zoom.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Secular+One&display=swap" rel="stylesheet">
    <link href="/css/book.css" rel="stylesheet">
</head>
<body style="background-color: transparent;   display: flex;
  justify-content: center;
  align-items: center;
  height: 600px;">
<div>

    <x-book :pages="$book->toBookArray()" :height="420"></x-book>
</div>
</body>
</html>
