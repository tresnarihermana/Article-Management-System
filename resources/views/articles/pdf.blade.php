<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $article->title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Arial', sans-serif;
            font-size: 10.5pt;
            line-height: 1.6;
            margin: 2.5cm 2cm;
            text-align: justify;
            color: #212529;
        }
        
        h1 {
            font-family: 'Helvetica Neue', 'Arial Black', sans-serif;
            font-size: 26pt;
            text-align: center;
            margin-bottom: 0.8em;
            color: #000000; /* Warna Judul diubah menjadi Hitam Penuh */
            padding-bottom: 0.2em;
            border-bottom: 3px solid #dee2e6; /* Garis pemisah yang netral */
            line-height: 1.1;
        }
        
        h2 {
            font-size: 16pt;
            margin-top: 2em;
            margin-bottom: 0.6em;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }

        h3 {
            font-size: 12pt;
            margin-top: 1.5em;
            margin-bottom: 0.4em;
            color: #444;
        }

        p {
            margin-bottom: 1.2em;
            text-indent: 0;
        }

        img {
            display: block;
            margin: 2em auto;
            max-width: 90%;
            height: auto;
            border: 1px solid #dee2e6;
            padding: 5px;
            border-radius: 4px;
        }
        
        .cover-image {
            max-width: 500px !important;
            margin-bottom: 2.5em !important;
            border: none !important;
            padding: 0 !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        ul, ol {
            margin: 1.5em 0;
            padding-left: 2em;
        }

        li {
            margin-bottom: 0.7em;
        }

        blockquote {
            margin: 1.5em 0;
            padding: 15px 25px;
            border-left: 5px solid #6c757d; /* Border warna abu-abu netral */
            background-color: #f8f9fa; /* Background abu-abu sangat muda */
            color: #333;
            font-style: italic;
            border-radius: 3px;
        }

        @page {
            margin: 2.5cm 2cm 2cm 2cm;
            counter-increment: page;
        }
        .page-footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: right;
            font-size: 9pt;
            color: #6c757d;
        }
        .page-footer:after {
            content: "Halaman " counter(page);
        }
    </style>
</head>
<body>
    <h1>{{ $article->title }}</h1>

    @if($article->cover)
        <div style="text-align:center;">
            <img src="{{ public_path('storage/' . $article->cover) }}" 
                class="cover-image">
        </div>
    @endif

    {!! $article->body !!}
    
    <div class="page-footer"></div>
</body>
</html>