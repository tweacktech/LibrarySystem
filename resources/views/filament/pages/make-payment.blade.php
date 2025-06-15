<x-filament-panels::page>
    <x-filament::section>
        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-2xl font-bold tracking-tight">
                    Make a Payment
                </h2>
                <p class="mt-2 text-gray-500">
                    Select a book and payment type to proceed with your payment
                </p>
            </div>

            <form wire:submit="makePayment" class="space-y-6">
                {{ $this->form }}

                <div class="flex justify-center">
                    <x-filament::button
                        type="submit"
                        size="lg"
                        class="w-full"
                    >
                        Proceed to Payment
                    </x-filament::button>
                </div>
            </form>
        </div>
    </x-filament::section>

    @push('scripts')
        <script src="https://js.paystack.co/v1/inline.js"></script>
    @endpush
</x-filament-panels::page>
