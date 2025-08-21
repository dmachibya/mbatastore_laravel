<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class PdfTemplateUpateRequest extends Request
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return $this->user()->isAdmin();
    // return $this->user()->can('create', \App\Models\Customer::class);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required',
      'type' => 'required',
      // 'description' => 'nullable',
      'is_default' => 'required',
      'active' => 'required',
    ];
  }
}
