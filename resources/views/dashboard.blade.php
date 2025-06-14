<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Borrowed Books</h3>
                    
                    @if($borrowedBooks->isEmpty())
                        <p class="text-gray-600">No books currently borrowed.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrow Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($borrowedBooks as $borrow)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $borrow->book->title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $borrow->borrow_date->format('Y-m-d') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $borrow->due_date->format('Y-m-d') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($borrow->is_lost)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Lost
                                                    </span>
                                                @elseif($borrow->status === 'returned')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Returned
                                                    </span>
                                                @elseif($borrow->due_date->isPast())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Overdue
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Borrowed
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if($borrow->status === 'borrowed')
                                                    <form method="POST" action="{{ route('books.return', $borrow) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900">Return</button>
                                                    </form>
                                                    <span class="mx-2">|</span>
                                                    <a href="{{ route('books.lost.form', $borrow) }}" class="text-red-600 hover:text-red-900">Mark as Lost</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 