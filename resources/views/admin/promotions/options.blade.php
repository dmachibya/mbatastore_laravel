@extends('admin.layouts.master')

@section('content')
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">{{ trans('app.promotions') }}</h3>
    </div> <!-- /.box-header -->
    <div class="box-body responsive-table">
      <table class="table table-striped">
        <thead>
          <tr>
            <th width="45%">@lang('app.options')</th>
            <th>@lang('app.values')</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          @if (Auth::user()->isFromPlatform())
            <tr>
              <th>
                <h4>@lang('app.promotional_tagline')</h4>
                <small class="text-muted">
                  {{ trans('help.promotional_tagline') }}
                </small>
              </th>
              <td>
                {{ trans('app.form.text') . ' : ' }}<strong>{{ empty($tagline['text']) ? '' : $tagline['text'] }}</strong>
                <br />
                {{ trans('app.action_url') . ' : ' }}<strong>{{ !empty($tagline['action_url']) ? $tagline['action_url'] : '' }}</strong>
              </td>
              <td class="text-right">
                <a href="javascript:void(0)" data-link="{{ route('admin.promotion.tagline') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>
              </td>
            </tr>

            <tr>
              <th>
                <h4>@lang('app.promo_banner')</h4>
                <small class="text-muted">
                  {{ trans('help.promo_banner') }}
                </small>
              </th>
              <td>
                @isset($top_bar_banner['img'])
                  <div class="form-group text-center d-flex">
                    <img src="{{ get_storage_file_url($top_bar_banner['img'], 'full') }}" class="popup-bg-img" width="100%" alt="{{ trans('app.top_bar_img') }}">
                  </div>
                @endisset

                @isset($top_bar_banner['action_url'])
                  <small>
                    {{ trans('app.action_url') . ' : ' }}<strong>{{ $top_bar_banner['action_url'] }}</strong>
                  </small>
                @endisset
              </td>
              <td class="text-right">
                <a href="javascript:void(0)" data-link="{{ route('admin.promotion.topBanner') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>
                <a href="{{ route('admin.promotion.topBanner.delete') }}" class="btn btn-sm btn-link flat"><i class="fa fa-trash"></i> @lang('app.delete')</a>
              </td>
            </tr>
          @endif

          <tr>
            <th>
              <h4>@lang('app.best_finds_under')</h4>
              <small class="text-muted">
                {!! trans('help.best_finds_under') !!}
              </small>
            </th>
            <td>
              <strong>
                {{ get_formated_currency($best_finds_under, 2, config('system_settings.currency.id')) }}
              </strong>
            </td>
            <td class="text-right">
              <a href="javascript:void(0)" data-link="{{ route('admin.promotion.bestFindsUnder') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>
            </td>
          </tr>

          <tr>
            <th>
              <h4>@lang('app.deal_of_the_day')</h4>
              <small class="text-muted">
                {!! trans('help.deal_of_the_day') !!}
              </small>
            </th>
            <td>
              @if ($deal_of_the_day)
                <span class="label label-outline">{{ $deal_of_the_day->title . ' | ' . $deal_of_the_day->sku . ' | ' . get_formated_currency($deal_of_the_day->current_sale_price(), 2, config('system_settings.currency.id')) }}</span>
              @endif
            </td>
            <td class="text-right">
              <a href="javascript:void(0)" data-link="{{ route('admin.promotion.dealOfTheDay') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>
            </td>
          </tr>

          <tr>
            <th>
              <h4>@lang('app.featured_items')</h4>
              <small class="text-muted">
                {!! trans('help.featured_items') !!}
              </small>
            </th>
            <td>
              @if ($featured_items)
                @foreach ($featured_items as $item)
                  <span class="label label-outline">{!! $item->title . ' | ' . $item->sku . ' | ' . get_formated_currency($item->current_sale_price(), 2, config('system_settings.currency.id')) !!}</span>
                @endforeach
              @endif
            </td>
            <td class="text-right">
              <a href="javascript:void(0)" data-link="{{ route('admin.featuredItems.edit') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>
            </td>
          </tr>

          @isset($featured_categories)
            <tr>
              <th>
                <h4>@lang('app.featured_categories')</h4>
                <small class="text-muted">
                  {!! trans('help.featured_categories') !!}
                </small>
              </th>
              <td>
                @foreach ($featured_categories as $category)
                  <span class="label label-outline">{{ $category }}</span>
                @endforeach
              </td>
              <td class="text-right">
                <a href="javascript:void(0)" data-link="{{ route('admin.promotion.featuredCategories.edit') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>
              </td>
            </tr>
          @endisset

          @isset($trending_categories)
            <tr>
              <th>
                <h4>@lang('app.trending_now_categories')</h4>
                <small class="text-muted">
                  {!! trans('help.trending_now_categories') !!}
                </small>
              </th>
              <td>
                @foreach ($trending_categories as $category)
                  <span class="label label-outline">{{ $category }}</span>
                @endforeach
              </td>
              <td class="text-right">
                <a href="javascript:void(0)" data-link="{{ route('admin.promotion.trendingNow.edit') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>
              </td>
            </tr>
          @endisset

          @isset($featured_brands)
            <tr>
              <th>
                <h4>@lang('app.featured_brands')</h4>
                <small class="text-muted">
                  {!! trans('help.featured_brands') !!}
                </small>
              </th>

              <td>
                @foreach ($featured_brands as $brand)
                  <span class="label label-outline">{{ $brand->name }}</span>
                @endforeach
              </td>

              <td class="text-right">
                <a href="javascript:void(0)" data-link="{{ route('admin.featuredBrands.edit') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>

                {{-- <a href="javascript:void(0)" data-link="{{ route('admin.appearance.featuredBrands') }}" class="ajax-modal-btn btn btn-sm btn-default flat"><i class="fa fa-edit"></i> @lang('app.edit')</a> --}}
              </td>
            </tr>
          @endisset

          @isset($featured_vendors)
            <tr>
              <th>
                <h4>@lang('app.featured_vendors')</h4>
                <small class="text-muted">
                  {!! trans('help.featured_vendors') !!}
                </small>
              </th>
              <td>
                @foreach ($featured_vendors as $vendors)
                  <span class="label label-outline">{{ $vendors->name }}</span>
                @endforeach
              </td>
              <td class="text-right">
                <a href="javascript:void(0)" data-link="{{ route('admin.featuredVendors.edit') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>

                {{-- <a href="javascript:void(0)" data-link="{{ route('admin.appearance.featuredBrands') }}" class="ajax-modal-btn btn btn-sm btn-default flat"><i class="fa fa-edit"></i> @lang('app.edit')</a> --}}
              </td>
            </tr>
          @endisset

          @isset($main_nav_categories)
            <tr>
              <th>
                <h4>@lang('app.show_category_on_main_nav')</h4>
                <small class="text-muted">
                  {!! trans('help.show_category_on_main_nav') !!}
                </small>
              </th>
              <td>
                @forelse ($main_nav_categories as $category)
                  <span class="label label-outline">{{ $category->name }}</span>
                @empty
                  <a href="javascript:void(0)" data-link="{{ route('admin.promotion.navCategories.edit') }}" class="ajax-modal-btn">
                    <em class="text-info">{{ trans('app.select_categories') }}</em>
                  </a>
                @endforelse
              </td>
              <td class="text-right">
                <a href="javascript:void(0)" data-link="{{ route('admin.promotion.navCategories.edit') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>
              </td>
            </tr>
          @endisset

          @isset($hidden_menu_items)
            <tr>
              <th>
                <h4>@lang('app.hide_item_from_main_nav')</h4>
                <small class="text-muted">
                  {!! trans('help.hide_item_from_main_nav') !!}
                </small>
              </th>
              <td>
                @foreach ($hidden_menu_items as $hidden_item)
                  <span class="label label-outline">{{ $hidden_item }}</span>
                @endforeach
              </td>
              <td>
                <a href="javascript:void(0)" data-link="{{ route('admin.promotion.navigation.edit') }}" class="ajax-modal-btn btn btn-sm btn-link flat"><i class="fa fa-edit"></i> @lang('app.edit')</a>
              </td>
            </tr>
          @endisset
        </tbody>
      </table>
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->
@endsection
