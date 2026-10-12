<!doctype html>
<html lang="en-US">
<head>
<meta charset="utf-8">
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="canonical" href="{{ url('/en/') }}">
<link rel="alternate" hreflang="id-ID" href="{{ url('/id/') }}">
<link rel="alternate" hreflang="en-US" href="{{ url('/en/') }}">
<link rel="alternate" hreflang="x-default" href="{{ url('/en/') }}">
<meta http-equiv="refresh" content="0; url=/en/">
{{-- A manual language choice beats the default; English is the default. --}}
<script>try{var l=localStorage.getItem('gb_lang');location.replace('/'+(l==='id'?'id':'en')+'/'+location.hash)}catch(e){}</script>
</head>
<body><a href="/en/">English</a> | <a href="/id/">Bahasa Indonesia</a></body>
</html>
