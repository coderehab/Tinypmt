<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">

        <title>Tinypmt</title>
        <meta name="description" content="Simple project manager">
        <meta name="author" content="Code.Rehab">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        {!! HTML::style('assets/css/app.css') !!}

        <!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
    </head>

    <body>

        <header>
            @include('template.header')
        </header>

        <aside>
            @include('template.sidebar')
        </aside>

        <article>
            @yield('page-content')
        </article>

        <script src="js/scripts.js"></script>
    </body>
</html>
