<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            // @TODO implement
            'id' => $this->id, 
            'isbn' => $this->isbn, 
            'title' => $this->title, 
            'description' => $this->description, 
            'published_year' => $this->published_year, 
            "authors" => [
                [
                    "id" => $this->authors == [] ? '' : $this->authors[0]->id,
                    "name" => $this->authors == [] ? '' : $this->authors[0]->name,
                    "surname" => $this->authors == [] ? '' : $this->authors[0]->surname,

                ]
             ],
            'review' => [
                'avg' => $this->avg,
                'count' => $this->count
            ]
        ];
    }
}
