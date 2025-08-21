<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use Incevio\Package\Payfast\Services\PayfastPaymentService;


interface PaymentMethodContract
{
  /**
   * This will be return/redirect end-point after payment. 
   * Use this end-point as the success return point point 
   * when a service needs multiple redirect points
   *
   * @param Request $request
   * @param PayfastPaymentService $payfast
   * @param string $order_ids
   * @return void
   */
  public function orderReturn(Request $request, PayfastPaymentService $payfast, string $order_ids);

  public function depositReturn(Request $request);
}
