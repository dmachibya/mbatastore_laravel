<div role="tabpanel" class="mb-5">
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
      <a href="#account-info-tab" aria-controls="account-info-tab" role="tab" data-toggle="tab" aria-expanded="true">@lang('theme.basic_info')</a>
    </li>

    @if (is_incevio_package_loaded('buyerGroup'))
      <li role="presentation">
        <a href="#buyer-group-tab" aria-controls="buyer-group-tab" role="tab" data-toggle="tab" aria-expanded="false">@lang('packages.buyer_group')</a>
      </li>
    @endif

    <li role="presentation">
      <a href="#password-tab" aria-controls="password-tab" role="tab" data-toggle="tab" aria-expanded="false">@lang('theme.change_password')</a>
    </li>

    <li role="presentation">
      <a href="#address-tab" aria-controls="address-tab" role="tab" data-toggle="tab" aria-expanded="false">@lang('theme.addresses')</a>
    </li>

    <li role="presentation">
      <a href="#delete-tab" aria-controls="delete-tab" role="tab" data-toggle="tab" aria-expanded="false">@lang('theme.button.delete')</a>
    </li>
  </ul><!-- /.nav-tabs -->

  <div class="tab-content">
    <div role="tabpanel" class="tab-pane fade active in" id="account-info-tab">
      @if (!customer_can_register())
        @include('partials.update_on_merchant_account_notice')
      @else
        <div class="row">
          <div class="col-md-8">
            <div class="text-center"></div>
            {!! Form::model($account, ['method' => 'PUT', 'route' => 'account.update', 'class' => 'form-horizontal mt-3', 'data-toggle' => 'validator']) !!}
            <div class="form-group">
              {!! Form::label('name', trans('theme.full_name') . '*', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-md-8 col-sm-12">
                {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'placeholder' => trans('theme.placeholder.full_name'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('nice_name', trans('theme.nice_name'), ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-md-8 col-sm-12">
                {!! Form::text('nice_name', null, ['id' => 'nice_name', 'class' => 'form-control', 'placeholder' => trans('theme.placeholder.nice_name')]) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('email', trans('theme.email') . '*', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-md-7 col-sm-10">
                {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('theme.placeholder.email'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.email_fill_notice') }}"></i>
            </div>

            @if (is_incevio_package_loaded('otp-login'))
              <div class="form-group">
                {!! Form::label('phone', trans('otp-login::lang.phone') . '*', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-md-8 col-sm-12">
                  {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => trans('otp-login::lang.valid_phone'), 'id' => 'phone', 'required']) !!}
                  <div class="help-block with-errors"></div>
                </div>
              </div>
            @endif

            <div class="form-group">
              {!! Form::label('dob', trans('theme.dob'), ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-md-8 col-sm-12">
                <div class="input-group">
                  {!! Form::text('dob', null, ['class' => 'form-control rounded-0 datepicker', 'placeholder' => trans('theme.placeholder.dob')]) !!}
                  <span class="input-group-addon"><i class="fas fa-calendar no-fill"></i></span>
                </div>
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('description', trans('theme.bio'), ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-md-8 col-sm-12">
                {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '4', 'placeholder' => trans('theme.placeholder.bio')]) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="form-group">
              <div class="col-sm-4">
                <small class="help-block text-muted pull-right">* {{ trans('theme.help.required_fields') }}</small>
              </div>
              <div class="col-sm-8 text-right">
                {!! Form::submit(trans('theme.button.update'), ['class' => 'btn btn-primary py-2 px-5']) !!}
              </div>
            </div>
            {!! Form::close() !!}
          </div><!-- /.col-md-8 -->

          <div class="col-md-4">
            <div class="user-avatar-section text-center">
              <div class="form-group">
                @if ($account->image)
                  {!! Form::model($account, ['method' => 'DELETE', 'route' => 'my.avatar.remove', 'class' => 'form-horizontal', 'data-toggle' => 'validator']) !!}
                  <button class="btn btn-xs btn-default confirm pull-right rounded-0" data-confirm="@lang('theme.confirm_action.delete')" type="submit" data-toggle="tooltip" data-title="{{ trans('theme.button.delete') }}" data-placement="left"><i class="fas fa-trash no-fill"></i></button>
                  {!! Form::close() !!}
                @endif

                {!! Form::label('avatar', trans('theme.avatar')) !!}
                <img class="thumbnail center-block lazy" src="{{ get_storage_file_url(optional($account->image)->path, 'tiny_thumb') }}" data-src="{{ get_storage_file_url(optional($account->image)->path, 'full') }}" alt="{{ trans('theme.avatar') }}" />
              </div>

              {!! Form::open(['route' => 'my.avatar.save', 'files' => true, 'data-toggle' => 'validator']) !!}
              <div class="form-group mx-4 mb-4">
                {!! Form::file('avatar', ['required']) !!}
                <div class="help-block with-errors"></div>
              </div>
              <button type="submit" class="btn btn-default btn-sm">{{ trans('theme.button.change_avatar') }}</button>
              {!! Form::close() !!}
            </div>
          </div><!-- /col-md-4 -->
        </div>
      @endif
    </div><!-- /#account-info-tab -->

    @if (is_incevio_package_loaded('buyerGroup'))
      <div role="tabpanel" class="tab-pane fade" id="buyer-group-tab">
        @include('buyerGroup::frontend.buyer_group_tab')
      </div> <!-- /#buyer-group-tab -->
    @endif

    <div role="tabpanel" class="tab-pane fade" id="password-tab">
      @if (!customer_can_register())
        @include('partials.update_on_merchant_account_notice')
      @else
        <div class="row">
          <div class="col-md-8 col-sm-offset-1">
            {!! Form::model($account, ['method' => 'PUT', 'route' => 'my.password.update', 'class' => 'form-horizontal', 'data-toggle' => 'validator']) !!}
            @if ($account->password)
              <div class="form-group">
                {!! Form::label('current_password', trans('theme.current_password') . '*', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-md-8">
                  {!! Form::password('current_password', ['class' => 'form-control', 'id' => 'current_password', 'placeholder' => trans('theme.placeholder.current_password'), 'data-minlength' => '6', 'required']) !!}
                  <div class="help-block with-errors"></div>
                </div>
              </div>
            @endif

            <div class="form-group">
              {!! Form::label('password', trans('theme.new_password') . '*', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-md-8">
                {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => trans('theme.placeholder.password'), 'data-minlength' => '6', 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('password_confirmation', trans('theme.confirm_password') . '*', ['class' => 'col-sm-4 control-label']) !!}
              <div class="col-md-8">
                {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => trans('theme.placeholder.confirm_password'), 'data-match' => '#password', 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="form-group">
              <div class="col-sm-4">
                <small class="help-block text-muted">* {{ trans('theme.help.required_fields') }}</small>
              </div>
              <div class="col-sm-8 text-right">
                {!! Form::submit(trans('theme.button.update'), ['class' => 'btn btn-primary']) !!}
              </div>
            </div>
            {!! Form::close() !!}
          </div><!-- /col-md-8 -->
          <div class="col-md-3"></div>
        </div>
      @endif
    </div><!-- /#password-tab -->

    <div role="tabpanel" class="tab-pane fade" id="address-tab">
      <div class="row">
        <div class="col-md-12 mb-4">
          @forelse($account->addresses as $address)
            <div class="col-md-4">
              @if (config('system_settings.address_show_map'))
                <iframe class="customer-map" width="100%" height="450" src="https://maps.google.com/maps?width=100%&amp;height=600&amp;hl=en&amp;coord=52.70967533219885, -8.020019531250002&amp;q={{ $address->address_line_1, $address->city }}&amp;ie=UTF8&amp;t=&amp;z=14&amp;iwloc=B&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
              @endif
            </div>
            <div class="col-md-8">
              {!! $address->toHtml() !!}
            </div>

            <div class="btn-group pull-right mb-3" role="group" aria-label="..." style="margin-top: -100px;">
              <a href="{{ route('my.address.delete', $address->id) }}" class="confirm btn btn-default btn-xs rounded-0 pull-right" data-confirm="@lang('theme.confirm_action.delete')">
                <i class="fas fa-trash no-fill"></i> @lang('theme.button.delete')
              </a>

              <a href="{{ route('my.address.edit', $address) }}" class="modalAction btn btn-default btn-xs rounded-0 pull-right">
                <i class="fas fa-edit"></i> @lang('theme.edit')
              </a>
            </div>
            <hr class="dotted my-2" />
          @empty
            <p class="lead text-center my-5">
              @lang('theme.nothing_found')
            </p>
          @endforelse
        </div>

        <div class="col-sm-12 text-center">
          <a href="{{ route('my.address.create') }}" class="modalAction btn btn-black">
            <i class="fas fa-address-card-o"></i> @lang('theme.button.add_new_address')
          </a>
        </div>
      </div>
    </div><!-- /#address-tab -->

    <div role="tabpanel" class="tab-pane fade" id="delete-tab">
      <div class="alert alert-danger mt-3">
        <strong>
          <i class="icon fas fa-info-circle no-fill"></i> {{ trans('app.notice') }}
        </strong>

        {{ trans('messages.account_delete') }}
      </div>

      {!! Form::model($account, ['method' => 'DELETE', 'route' => 'my.account.remove', 'class' => 'form-horizontal', 'data-toggle' => 'validator']) !!}

      <div class="text-center my-4">
        <button class="btn btn-danger px-5 py-2 confirm" data-confirm="{{ trans('theme.confirm_action.delete') }}" type="submit" data-toggle="tooltip" data-title="{{ trans('theme.button.delete') }}" data-placement="left">
          <i class="fas fa-trash no-fill mr-2"></i>
          {{ trans('theme.button.delete') }}
        </button>
      </div>

      {!! Form::close() !!}
    </div><!-- /#delete-tab -->
  </div><!-- /.tabpanel -->
