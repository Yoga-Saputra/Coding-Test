<?php

namespace App\Http\Controllers;

use App\Book;
use App\Author;
use App\BookReview;
use DB;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;


class BooksController extends Controller
{
    public function __construct($data = null, $status = 200, $headers = [], $options = 0, $json = false){
        
    }

    public function index(Request $request)
    {
        // @TODO implement
        $title = '%'.$request->title.'%';
        $author = '%'.$request->author.'%';
        // $book = Book::paginate(15);
        $book = BookReview::join('books', 'books.id', '=', 'book_reviews.book_id')
        ->join('users', 'users.id', '=', 'book_reviews.user_id')
        ->select(
            'users.id as userId',
            'users.name',

            'books.id as id',
            'books.isbn',
            'books.title',
            'books.description',
            'books.published_year',
            
            'book_reviews.review',
            DB::raw('round(AVG(book_reviews.review),0) as avg'),
            DB::raw('COUNT(book_reviews.review) as count')
        )
        ->where('books.title', 'like', $title)
        ->orWhere('book_reviews.user_id', 'like', $author)
        ->groupBy('book_reviews.id', 'book_reviews.book_id');

        if ($request->sortColumn == 'title') {
            $title = $book->orderBy('books.title', $request->sortDirection)->paginate(15);
            return BookResource::collection($title);
        }
        if ($request->sortColumn == 'avg_review') {
            $avg_review = $book->orderBy('book_reviews.review', $request->sortDirection)->paginate(15);
            return BookResource::collection($avg_review);
        }
        if ($request->sortColumn == 'published_year') {
            $published_year = $book->orderBy('books.published_year', $request->sortDirection)->paginate(15);
            return BookResource::collection($published_year);
        }

        return BookResource::collection($book->paginate(15));
    }

    public function store(PostBookRequest $request)
    {
        // @TODO implement
        $validator = Validator::make($request->all(), [
            'isbn' => 'required|unique:books|min:13|max:13',
            'title' => 'required|string',
            'description' => 'required|string',
            'authors'   => 'required|array',
            'authors.*'   => 'integer',
            'published_year' => 'required|integer|between:1900,2020',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $author = Author::find($request->authors[0]);
        if (is_null($author)) {
            return response()->json('author format not correct', 422);
        }
        

        // 'name' => $author ? $author->name : '',
        // 'surname' => $author ? $author->surname : '',
        // 'authors' => $request->authors[0],
        $payload = [
            'isbn' => $request->isbn,
            'title' => $request->title,
            'description' => $request->description,
            'published_year' => $request->published_year
        ];
        
        $book = Book::create($payload);
        $book->authors()->attach($author);
        return (new BookResource($book, 201));
    }
}
