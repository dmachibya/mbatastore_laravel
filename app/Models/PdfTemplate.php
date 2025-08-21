<?php

namespace App\Models;

use App\Models\PdfTemplate as Template;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends BaseModel
{
    public const TYPE_ORDER_INVOICE = 'order_invoice';
    public const TYPE_SHIPPING_LABEL = 'shipping_label';
    public const TYPE_WALLET_TRANSACTION = 'wallet_transaction';
    public const TYPE_AFFILIATE_WALLET_TRANSACTION = 'affiliate_transaction';

    protected $table = 'pdf_templates';

    protected $fillable = [
        'shop_id',
        'name',
        'type',
        'description',
        'file_name',
        'path',
        'is_default',
        'active',
    ];

    /**
     * The attributes that should be casted to boolean types.
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Get the full path for the template
    public function getDefaultTemplatePathAttribute()
    {
        return Template::where('is_default', true)->where('type', $this->type)->first()->path;
    }

    public function getBladeTemplateNameAttribute()
    {
        return basename($this->path, '.blade.php');
    }
}
