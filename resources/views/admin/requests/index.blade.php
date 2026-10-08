<x-layouts.admin title="Usulan buku">
    <div class="max-w-5xl">
        <h1 class="text-3xl font-semibold">Usulan buku</h1>
        <p class="mt-1 text-ink-2">Judul yang diminta mahasiswa. Status yang kamu pilih langsung tampil di akun pengusul.</p>

        <nav class="mt-5 flex flex-wrap gap-1 rounded-md border border-line bg-surface p-1" aria-label="Filter status">
            <a href="{{ route('admin.requests.index') }}" class="{{ ! $status ? 'nav-link-active' : 'nav-link' }} py-1.5">Semua</a>
            @foreach ($statuses as $key => $label)
                <a href="{{ route('admin.requests.index', ['status' => $key]) }}" class="{{ $status === $key ? 'nav-link-active' : 'nav-link' }} py-1.5">{{ $label }}</a>
            @endforeach
        </nav>

        @forelse ($requests as $request)
            <article class="card mt-4 flex flex-col gap-4 p-5 sm:flex-row">
                @if ($request->image)
                    <img src="{{ \App\Support\Media::url($request->image, 200) }}" alt="Sampul usulan {{ $request->title }}" class="h-28 w-20 shrink-0 rounded-sm border border-line object-cover">
                @endif
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-semibold leading-snug">{{ $request->title }}</h2>
                    <p class="text-sm text-ink-2">{{ $request->author }}{{ $request->publisher ? ' · '.$request->publisher : '' }}{{ $request->category ? ' · '.$request->category : '' }}</p>
                    <blockquote class="mt-2 border-l-2 border-line-strong pl-3 text-sm text-ink-2">{{ $request->reason }}</blockquote>
                    <p class="mt-2 text-xs text-muted">Diusulkan {{ $request->student->name }} ({{ $request->student->nim }}) · {{ tanggal($request->created_at) }}</p>
                </div>
                <form method="POST" action="{{ route('admin.requests.update', $request) }}" class="flex shrink-0 items-start gap-2 sm:flex-col sm:items-stretch">
                    @csrf
                    @method('PATCH')
                    <label for="status-{{ $request->id }}" class="sr-only">Status</label>
                    <select id="status-{{ $request->id }}" name="status" class="input w-52">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($request->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="btn-secondary btn-sm">Simpan status</button>
                </form>
            </article>
        @empty
            <x-empty class="mt-6" title="Tidak ada usulan di sini" icon="lightbulb" />
        @endforelse

        <div class="mt-5">{{ $requests->links() }}</div>
    </div>
</x-layouts.admin>
