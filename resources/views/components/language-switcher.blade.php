@php
    $currentLocale = app()->getLocale();
@endphp

<form action="{{ route('locale.switch') }}" method="POST" class="inline-flex items-center">
    @csrf
    <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-0.5 text-xs font-semibold">
        <button type="submit" name="locale" value="en"
                class="px-2.5 py-1 rounded-md transition-colors {{ $currentLocale === 'en' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            EN
        </button>
        <button type="submit" name="locale" value="sr_Latn"
                class="px-2.5 py-1 rounded-md transition-colors {{ $currentLocale === 'sr_Latn' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            SR
        </button>
    </div>
</form>
