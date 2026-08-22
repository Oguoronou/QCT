@extends('layout')

@section('content')
<section class="py-16 px-6">
    <div class="container mx-auto max-w-[820px]">
        <h1 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight mb-10">Questions fréquentes</h1>

        <div class="flex flex-col gap-3" id="faqAccordion">
            @forelse ($faqItems as $faqItem)
            <div class="bg-slate-800 border border-slate-700 rounded-[14px] overflow-hidden">
                <button type="button"
                        class="faq-toggle w-full flex items-center justify-between text-left px-5 py-4 text-slate-50 font-semibold cursor-pointer bg-transparent border-none">
                    <span>{{ $faqItem->question }}</span>
                    <i class="fas fa-chevron-down text-slate-400 text-sm transition-transform"></i>
                </button>
                <div class="faq-answer hidden px-5 pb-4 text-sm text-slate-400 leading-relaxed">
                    {{ $faqItem->answer }}
                </div>
            </div>
            @empty
            <p class="text-slate-400">Aucune question pour le moment.</p>
            @endforelse
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.faq-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var answer = btn.nextElementSibling;
        var icon = btn.querySelector('i');
        answer.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    });
});
</script>
@endsection
