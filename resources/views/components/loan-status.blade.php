@props(['loan'])
@switch(true)
    @case($loan->status === 'pending')
        <span class="badge-brass"><x-icon name="clock" :size="13" /> Menunggu diambil</span>
        @break
    @case($loan->isOverdue())
        <span class="badge-danger"><x-icon name="alert" :size="13" /> Telat {{ $loan->daysLate() }} hari</span>
        @break
    @case($loan->status === 'active' && $loan->type === 'online')
        <span class="badge-brand">E-book aktif</span>
        @break
    @case($loan->status === 'active')
        <span class="badge-brand">Dipinjam</span>
        @break
    @case($loan->status === 'returned' && $loan->hasUnpaidFine())
        <span class="badge-danger">Denda belum lunas</span>
        @break
    @case($loan->status === 'returned')
        <span class="badge-neutral">Dikembalikan</span>
        @break
    @case($loan->status === 'expired')
        <span class="badge-neutral">Tiket hangus</span>
        @break
    @case($loan->status === 'rejected')
        <span class="badge-neutral">Ditolak</span>
        @break
    @default
        <span class="badge-neutral">Dibatalkan</span>
@endswitch
