@switch($status)
    @case('approved') <span class="badge-brand">Disetujui, akan dibeli</span> @break
    @case('available') <span class="badge-brand">Sudah tersedia</span> @break
    @case('rejected') <span class="badge-neutral">Tidak dibeli</span> @break
    @default <span class="badge-brass">Menunggu ditinjau</span>
@endswitch
