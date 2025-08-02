<?php
namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function catalog(Request $request)
    {
        $query = Book::with(['author', 'genre', 'publisher'])
            ->where('available_copies', '>', 0);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('author', function($authorQuery) use ($search) {
                      $authorQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by genre
        if ($request->filled('genre')) {
            $query->where('genre_id', $request->genre);
        }

        // Filter by author
        if ($request->filled('author')) {
            $query->where('author_id', $request->author);
        }

        $books = $query->orderBy('title')->paginate(12);
        $genres = Genre::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();

        return view('pages.catalog', compact('books', 'genres', 'authors'));
    }

    public function about()
    {
        return view('pages.about');
    }
}
