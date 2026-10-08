<x-layouts.app title="Usulan buku">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold">Usulan buku</h1>
            <p class="mt-1 text-sm text-muted">Judul yang kamu minta supaya dibeli perpustakaan.</p>
        </div>
        <a href="{{ route('student.requests.create') }}" class="btn-primary"><x-icon name="plus" :size="16" /> Usulkan judul</a>
    </div>

    @if ($requests->isEmpty())
        <x-empty class="mt-8" title="Belum ada usulan" icon="lightbulb">
            Butuh buku yang belum ada di katalog? Usulkan, dan admin akan meninjaunya untuk pengadaan berikutnya.
        </x-empty>
    @else
        <ul class="mt-6 divide-y divide-line rounded-md border border-line bg-surface">
            @foreach ($requests as $request)
                <li class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="font-semibold">{{ $request->title }}</p>
                        <p class="text-sm text-muted">{{ $request->author }}{{ $request->publisher ? ' · '.$request->publisher : '' }} · diusulkan {{ tanggal($request->created_at) }}</p>
                    </div>
                    @include('requests._status', ['status' => $request->status])
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.app>
