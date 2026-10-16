@extends('layouts.admin')
@section('title', 'Pelanggan Newsletter')
@section('header', 'Newsletter')
@section('content')
<p class="mb-4 text-sm text-gray-500 dark:text-sage-300">{{ $subscribers->total() }} email terdaftar untuk info promo.</p>
<div class="mt-5 overflow-hidden rounded-3xl border border-line/70 bg-card dark:bg-night-800 dark:border-cream-100/15">
    <table class="w-full text-sm">
        <thead class="bg-milk-100 text-left text-xs uppercase tracking-wider text-cocoa-500/70 dark:bg-cream-100/5"><tr><th class="px-5 py-3.5">Email</th><th class="px-4 py-3.5 text-right">Terdaftar</th></tr></thead>
        <tbody class="divide-y divide-line/60 dark:divide-cream-100/10">
        @forelse($subscribers as $s)
        <tr class="hover:bg-milk-100/70 dark:hover:bg-cream-100/5 transition">
            <td class="px-5 py-3.5 font-bold">{{ $s->email }}</td>
            <td class="px-4 py-3.5 text-right text-cocoa-500/70">{{ $s->created_at->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="2" class="px-5 py-10 text-center text-cocoa-500/70">Belum ada pendaftar.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $subscribers->links() }}</div>
@endsection
