@php
    $school = \Filament\Facades\Filament::getTenant();
@endphp

<x-filament::section>
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-sm text-gray-500">Kode Undangan Sekolah</div>
            <div class="text-2xl font-semibold">
                {{ $school?->invite_code ?? '-' }}
            </div>
            <div class="text-xs text-gray-500 mt-1">
                Bagikan kode ini ke guru lain untuk bergabung ke sekolah yang sama.
            </div>
        </div>

        @if ($school?->invite_code)
            <x-filament::button color="gray" size="sm"
                x-on:click="navigator.clipboard.writeText('{{ $school->invite_code }}')">
                Copy
            </x-filament::button>
        @endif
    </div>
</x-filament::section>
