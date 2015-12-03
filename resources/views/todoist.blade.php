<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            header{
                padding:20px;
                position:absolute;
                top:0;
                left:0;
                right:0;
                height:50px;
                box-sizing:border-box;
                background:#222;
                z-index:10;
                color:#fff;
            }
            aside{
                background:#f2f2f2;
                border-right:1px solid #eee;
                padding:20px;
                position:absolute;
                top:50px;
                left:0;
                width:300px;
                bottom:0px;
                box-sizing:border-box;
                overflow:auto;
            }
            article{
                padding:20px;
                position:absolute;
                top:50px;
                left:300px;
                right:0;
                bottom:0;
                box-sizing:border-box;
                overflow:auto;
            }
        </style>
    </head>
    <body>
        <header>Tinypmt</header>
        <aside>
            <?php

echo "<ul>";
$indent = 1;
foreach ($projects as $project) {

    if ($project->indent < $indent) echo "</ul>";
    if ($project->indent > $indent) echo "<ul>";
    echo "<li>$project->name</li>";


    $indent = $project->indent;
}
echo "</ul>";
            ?>
        </aside>
        <article>

            <ul>
                @foreach($items as $item)
                <li>{{$item->content}}</li>
                @endforeach
            </ul>

        </article>
    </body>
</html>
