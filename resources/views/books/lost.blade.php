<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mark Book as Lost') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('books.lost', $bookBorrow) }}">
                        @csrf

                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Book Details</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Title: {{ $bookBorrow->book->title }}<br>
                                Borrowed by: {{ $bookBorrow->user->name }}<br>
                                Due date: {{ $bookBorrow->due_date->format('Y-m-d') }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">Replacement Price (Naira)</label>
                            <input type="number" step="0.01" min="0" name="price" id="price" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Mark as Lost and Process Payment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
