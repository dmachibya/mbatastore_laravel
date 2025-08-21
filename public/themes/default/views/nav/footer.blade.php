<footer>
  <div class="footer">
    <div class="container">
      <div class="footer-inner">
        <div class="footer-newsletter-box">
          <div class="row">
            <div class="col-lg-6 col-12">
              <div class="footer-newsletter-box-content">
                <div class="footer-newsletter-box-icon">
                  <img src="{{ theme_asset_url('img/mail.png') }}" alt="">
                </div>

                <div class="footer-newsletter-box-text">
                  <h3>{{ trans('theme.newsletter_subscribe') }}</h3>
                  <p class="mt-2">{{ trans('theme.newsletter_description') }}</p>
                </div>
              </div>
            </div> <!-- /.col-lg-6 col-12 -->

            <div class="col-lg-6 col-12">
              <div class="footer-newsletter-box-form">
                {!! Form::open(['route' => 'newsletter.subscribe', 'class' => 'form-inline subscribe-form', 'id' => 'form', 'data-toggle' => 'validator']) !!}
                <div class="footer-newsletter-box-form-box">
                  {!! Form::email('email', null, ['placeholder' => trans('theme.placeholder.email'), 'required']) !!}
                  <button type="submit">{{ trans('theme.button.subscribe') }}</button>
                </div>
                {!! Form::close() !!}
              </div>
            </div> <!-- /.col-lg-6 col-12 -->
          </div> <!-- /.row -->
        </div> <!-- /.footer-newsletter-box -->

        <div class="footer-content">
          <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
              <div class="footer-content-box">
                <div class="footer-content-box-inner footer-brand-info">
                  <div class="footer-logo">
                    <a href="{{ url('/') }}">
                      <img src="{{ get_logo_url('system', 'logo') }}" class="brand-logo" alt="{{ trans('app.logo') }}" title="{{ trans('app.logo') }}">
                    </a>
                  </div>

                  <div class="footer-content-box-text">
                    <p>{!! config('system_settings.slogan') !!}</p>
                  </div>

                  <div class="footer-content-box-location">
                    <div class="d-flex">
                      <i class="fas fa-map-marker-alt"></i>
                      <p>{!! get_platform_address_string() !!}</p>
                    </div>
                  </div>

                  @if (config('system_settings.support_phone'))
                    <div class="footer-content-box-number">
                      <a href="tel: {!! config('system_settings.support_phone') !!}">
                        <i class="fas fa-phone-alt"></i>{!! config('system_settings.support_phone') !!}
                      </a>
                    </div>
                  @endif

                  @if (config('system_settings.support_email'))
                    <div class="footer-content-box-number">
                      <a href="mailto:{!! config('system_settings.support_email') !!}">
                        <i class="fas fa-at"></i>{!! config('system_settings.support_email') !!}
                      </a>
                    </div>
                  @endif

                  <div class="footer-content-box-website">
                    <a href="{{ url('/') }}"><i class="fas fa-globe-asia"></i>{{ config('app.url') }}</a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-2 col-md-4  col-sm-6 col-6">
              <div class="footer-content-box">
                <div class="footer-content-box-inner">
                  <div class="footer-content-box-title">
                    <h3>{{ trans('theme.nav.let_us_help') }}</h3>
                  </div>
                  <div class="footer-content-box-links">
                    <ul>
                      <li>
                        <a href="{{ route('account', 'dashboard') }}" rel="nofollow">{{ trans('theme.nav.your_account') }}</a>
                      </li>

                      <li>
                        <a href="{{ route('account', 'orders') }}" rel="nofollow">{{ trans('theme.nav.your_orders') }}</a>
                      </li>

                      @foreach ($pages->where('position', 'footer_1st_column') as $page)
                        <li>
                          <a href="{{ get_page_url($page->slug) }}" rel="nofollow noopener" target="_blank">
                            {{ $page->title }}
                          </a>
                        </li>
                      @endforeach

                      <li>
                        <a href="{{ route('blog') }}" target="_blank" rel="noopener">{{ trans('theme.nav.blog') }}</a>
                      </li>
                    </ul>
                  </div> <!-- /.footer-content-box-links -->
                </div> <!-- /.footer-content-box-inner -->
              </div> <!-- /.footer-content-box -->
            </div>

            <div class="col-lg-2 col-md-4  col-sm-6 col-6">
              <div class="footer-content-box">
                <div class="footer-content-box-inner">
                  <div class="footer-content-box-title">
                    <h3>{{ trans('theme.nav.make_money') }}</h3>
                  </div>
                  <div class="footer-content-box-links">
                    <ul>
                      <li>
                        <a href="{{ url('/selling') }}">{{ trans('theme.nav.sell_on', ['platform' => get_platform_title()]) }}</a>
                      </li>
                      <li>
                        <a href="{{ url('/selling#pricing') }}">{{ trans('theme.nav.become_merchant') }}</a>
                      </li>
                      @if (is_incevio_package_loaded('affiliate'))
                        <li>
                          <a href="{{ route('affiliate.register.form') }}">{{ trans('packages.affiliate.become_an_affiliate') }}</a>
                        </li>
                      @endif
                      <li>
                        <a href="{{ url('/selling#howItWorks') }}">{{ trans('theme.nav.how_it_works') }}</a>
                      </li>
                      @foreach ($pages->where('position', 'footer_2nd_column') as $page)
                        <li>
                          <a href="{{ get_page_url($page->slug) }}" rel="nofollow" target="_blank" rel="noopener">
                            {{ $page->title }}
                          </a>
                        </li>
                      @endforeach
                      <li>
                        <a href="{{ url('/selling#faqs') }}" rel="nofollow">{{ trans('theme.nav.faq') }}</a>
                      </li>
                    </ul>
                  </div> <!-- /.footer-content-box-links -->
                </div> <!-- /.footer-content-box-inner -->
              </div> <!-- /.footer-content-box -->
            </div>

            <div class="col-lg-2 col-md-4  col-sm-6 col-6">
              <div class="footer-content-box">
                <div class="footer-content-box-inner">
                  <div class="footer-content-box-title">
                    <h3>{{ trans('theme.nav.customer_service') }}</h3>
                  </div>
                  <div class="footer-content-box-links">
                    <ul>
                      <li>
                        <a href="{{ route('account', 'disputes') }}">{{ trans('theme.nav.refunds_disputes') }}</a>
                      </li>
                      <li>
                        <a href="{{ route('account', 'orders') }}">{{ trans('theme.nav.contact_seller') }}</a>
                      </li>
                      @foreach ($pages->where('position', 'footer_3rd_column') as $page)
                        <li>
                          <a href="{{ get_page_url($page->slug) }}" rel="nofollow" target="_blank" rel="noopener">
                            {{ $page->title }}
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  </div> <!-- /.footer-content-box-links -->
                </div> <!-- /.footer-content-box-inner -->
              </div> <!-- /.footer-content-box -->
            </div>

            <div class="col-lg-3 col-md-4  col-sm-6 col-6">
              <div class="footer-content-box">
                <div class="footer-content-box-inner">
                  <div class="footer-content-box-title">
                    <h3>{{ trans('theme.stay_connected') }}</h3>
                  </div>

                  @if ($social_media_links = get_social_media_links())
                    <div class="footer-content-box-social">
                      <ul>
                        @foreach ($social_media_links as $social_media => $link)
                          <li>
                            <a href="{{ $link }}" target="_blank" rel="noopener" aria-label="{{ $social_media }}" title="{{ $social_media }}">
                              <i class="fa fa-{{ $social_media }}"></i>
                            </a>
                          </li>
                        @endforeach
                      </ul>

                      @if ($trust_badge = get_trust_badge_url())
                        <div class="mt-4 mb-2">
                          <img src="{{ $trust_badge }}" alt="{{ trans('theme.trust_badge') }}" />
                        </div>
                      @endif
                    </div> <!-- /.footer-content-box-social -->
                  @endif
                </div> <!-- /.footer-content-box-inner -->
              </div> <!-- /.footer-content-box -->
            </div> <!-- /.col-lg-3 -->
          </div> <!-- /.row -->
        </div> <!-- /.footer-content -->
      </div> <!-- /.footer-inner -->
    </div> <!-- /.container -->
  </div> <!-- /.footer -->
</footer>

<!-- COPYRIGHT AREA -->
@include('theme::nav.copyright')
