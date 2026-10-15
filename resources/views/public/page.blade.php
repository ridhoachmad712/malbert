@extends('layouts.app')
@section('title', $page->title)
@section('description', \Illuminate\Support\Str::limit(strip_tags($page->body), 150))
@section('content')
<article class="container public-page"><span class="eyebrow">INFORMASI SEKOLAH</span><h1>{{ $page->title }}</h1><div class="public-page-body">@foreach(preg_split('/\R\s*\R/', trim($page->body)) as $paragraph)<p>{!! nl2br(e(trim($paragraph))) !!}</p>@endforeach</div></article>
@endsection
