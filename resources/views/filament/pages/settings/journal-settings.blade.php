<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-4 justify-start">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>

    @script
        <script>
            $wire.on('buka-midtrans', (event) => {
                // Livewire 3 mengirim object, kita ambil properti 'token'
                const snapToken = event.token;

                if (window.snap) {
                    window.snap.pay(snapToken, {
                        onSuccess: function(result) {
                            window.location.reload();
                        },
                        onPending: function(result) {
                            window.location.reload();
                        },
                        onError: function(result) {
                            alert("Pembayaran gagal!");
                        }
                    });
                } else {
                    alert('Library Midtrans tidak ditemukan. Cek script di Provider.');
                }
            });
        </script>
    @endscript
</x-filament-panels::page>
