<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class AttributeDryResource extends JsonResource
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
            'id' => (int) $this->attribute_id,
            'name' => $this->getAttributeName(),
            'value' => $this->id,
            'color' => $this->color,
            'type_id' => $this->getAttributeType()['type_id'],
            'type_name' => $this->getAttributeType()['type_name'],
        ];
    }

    /**
     * Get attribute type of the resource by the attribute_id
     *
     * @return array
     */
    private function getAttributeType()
    {
        $attribute_type_id = DB::table('attributes')->where('id', $this->attribute_id)->value('attribute_type_id');
        $attribute_type_name = DB::table('attribute_types')->where('id', $attribute_type_id)->value('type');

        return [
            'type_id' => $attribute_type_id,
            'type_name' => $attribute_type_name,
        ];
    }

    /**
     * Get the attribute name of the resource
    */
    private function getAttributeName()
    {
        return DB::table('attributes')->where('id', $this->attribute_id)->value('name');
    }
}
