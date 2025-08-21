<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class PdfTemplateCreateRequest extends Request
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
    $this->merge(['shop_id' => $this->user()->merchantId()]); // Set shop_id

    return [
      'template' => 'required|file',
      'name' => 'required',
      'type' => 'required',
      // 'description' => 'nullable',
      'is_default' => 'required',
      'active' => 'required',
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array
   */
  public function messages()
  {
    return [
      // 'template.mimes' => trans('validation.csv_mimes'),
    ];
  }
}
