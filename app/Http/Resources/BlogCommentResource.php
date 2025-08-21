<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogCommentResource extends JsonResource
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
            'id' => $this->id,
            'content' => $this->content,
            'likes' => $this->likes,
            'dislikes' => $this->dislikes,
            'published_at' => date('F j, Y', strtotime($this->created_at)),
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->getName(),
                'avatar' => get_storage_file_url(optional($this->author->avatarImage)->path, 'small'),
            ],
        ];
    }
}
