<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body>
        <form action="{{route('json')}}" method="POST">
            @csrf
            <p><label for="text">Text:</label></p>
            <textarea id="text" name="text" rows="20" cols="100" placeholder="Put the text here"></textarea>
            <br>
            <input type="submit" value="Submit">
        </form>
    </body>
</html>
