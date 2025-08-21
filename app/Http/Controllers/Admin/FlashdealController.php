<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\PromotionAccessRequest;
use App\Http\Requests\Validations\FlashdealRequest;

class FlashdealController extends Controller
{
    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.flashdeals');
    }

    /**
     * Undocumented function
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PromotionAccessRequest $request)
    {
        $data = get_from_option_table('flashdeal_items', []);

        $start_time = isset($data['start_time']) ? $data['start_time'] : Null;
        $end_time = isset($data['end_time']) ? $data['end_time'] : Null;

        $listings = isset($data['listings']) ?
            Inventory::whereIn('id', $data['listings'])->get()->pluck('title', 'id')->toArray() : [];

        $featured = isset($data['featured']) ?
            Inventory::whereIn('id', $data['featured'])->get()->pluck('title', 'id')->toArray() : [];

        return view('admin.flashdeal.settings', compact('start_time', 'end_time', 'listings', 'featured'));
    }

    /**
     * Create deals
     *
     * @param FlashdealRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(FlashdealRequest $request)
    {
        $data = [
            'start_time' => Carbon::createFromDate($request->get('start_time')),
            'end_time' => Carbon::createFromDate($request->get('end_time')),
            'listings' => $request->listings,
            'featured' => $request->featured
        ];

        $create = DB::table(get_option_table_name())->updateOrInsert(
            ['option_name' => 'flashdeal_items'],
            [
                'option_name' => 'flashdeal_items',
                'option_value' => serialize($data),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        if ($create) {
            // Clear cached value
            Cache::forget('flashdeals');

            return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }
}
