@extends('layouts.app')
@section('title', $title . ' - Shopedia')
@section('content')
<div class="mx-auto max-w-3xl">
    <div class="flex flex-wrap gap-2 text-xs font-bold">
        @foreach($pages as $slug => $name)
        <a href="{{ route('info.show', $slug) }}" class="rounded-full px-4 py-2 transition {{ $name === $title ? 'bg-night-950 text-cream-100 dark:bg-cream-100 dark:text-night-950' : 'border border-line bg-card hover:bg-milk-100 dark:border-cream-100/15 dark:hover:bg-cream-100/10' }}">{{ $name }}</a>
        @endforeach
    </div>
    <div class="mt-5 rounded-3xl border border-line/70 bg-card p-7 sm:p-10 dark:bg-night-800 dark:border-cream-100/15">
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $title }}</h1>
        <div class="mt-4 space-y-4 text-[15px] leading-7 text-cocoa-700 dark:text-cream-100/80">
            @foreach($paragraphs as $i => $p)
            <p><span class="font-black text-ember-600 dark:text-sage-300">{{ $i + 1 }}.</span> {{ $p }}</p>
            @endforeach
        </div>
    </div>
</div>
@endsection
