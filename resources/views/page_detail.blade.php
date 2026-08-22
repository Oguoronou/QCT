@extends('layout')

@section('content')
<section class="py-16 px-6">
    <div class="container mx-auto max-w-[820px]">
        <h1 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight mb-8">{{ $page->title }}</h1>
        <div class="prose prose-invert prose-slate max-w-none text-slate-300 leading-relaxed">
            {!! $page->content !!}
        </div>
    </div>
</section>
@endsection
