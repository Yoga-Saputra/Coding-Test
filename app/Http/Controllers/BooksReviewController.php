<?php

namespace App\Http\Controllers;

use App\BookReview;
use App\Book;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BooksReviewController extends Controller
{
    public function __construct()
    {

    }

    public function store(int $bookId, PostBookReviewRequest $request)
    {
        try {
            // @TODO implement
            $book = Book::find($request->id);
            
            if (is_null($book)) {
                return response()->json([],404);
            }

            $validator = Validator::make($request->all(), [
                'comment' => 'required|string',
                'review' => 'required|integer|between:1,10',
            ]);

            if ($validator->fails()) {
                return response()->json([$validator->errors()->first()], 422);
            }
            $payload = [
                'book_id'   => $request->book_id,
                'user_id'   => $request->user_id,
                'review'    => $request->review,
                'comment'   => $request->comment,
            ];
            $bookReview = BookReview::create($payload);
            

            return (new BookReviewResource($bookReview))
                ->response()->setStatusCode(201);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        
    }

    public function destroy(int $bookId, int $reviewId, Request $request)
    {
        // @TODO implement
        $book = BookReview::where('book_id', '=', $bookId)->where('id', $reviewId)->first();
        if (!$book) {
            return response()->json([
                'status' => 'error',
                'message' => 'invalid book id'
            ],404);
        }
        $validator = Validator::make($request->all(), [
            'review' => 'required|integer|between:1,10',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()->first()], 204);
        }

        
        $book->delete();
        return response()->json('Book review deleted', 204);
    }
}
