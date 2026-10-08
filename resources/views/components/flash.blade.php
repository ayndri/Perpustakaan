@if (session('success'))
    <div role="status" class="flex items-start gap-3 rounded-md border border-brand/25 bg-brand-tint px-4 py-3 text-sm text-brand">
        <x-icon name="check" class="mt-0.5" />
        <p class="font-medium">{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div role="alert" class="flex items-start gap-3 rounded-md border border-danger/25 bg-danger-tint px-4 py-3 text-sm text-danger">
        <x-icon name="alert" class="mt-0.5" />
        <p class="font-medium">{{ session('error') }}</p>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div role="alert" class="rounded-md border border-danger/25 bg-danger-tint px-4 py-3 text-sm text-danger">
        <p class="font-semibold">Ada isian yang perlu dibetulkan:</p>
        <ul class="mt-1 list-disc space-y-0.5 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
