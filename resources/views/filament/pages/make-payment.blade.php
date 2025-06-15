<x-filament-panels::page>
    <x-filament::section>
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900">Make a Payment</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Please fill in the details below to make your payment for late return or lost book.
                </p>
            </div>

            <form wire:submit="makePayment">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit" class="w-full">
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
