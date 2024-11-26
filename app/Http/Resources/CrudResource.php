<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrudResource extends JsonResource
{
    public $type;
    public $message;
    public function __construct($type, $message, $resource)
    {
        parent::__construct($resource);
        $this->type  = $type;
        $this->message = $message;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'type'   => $this->type,
            'message'   => $this->message,
            'data'      => $this->resource,
        ];
    }
}
