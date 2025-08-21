<h4>{{ trans('app.example') }}:</h4>
@if (Auth::user()->isFromMerchant())
  <p class="text-info">
    {{ trans('help.custom_css_guideline_for_merchant') }}
  </p>

  <pre>
  .header-fullname {
    color: #fed700;
  }
  #profile-container .profile-header .thumbnail {
    width: 150px;
    height: 150px;
  }
</pre>
@else
  <p class="text-info">
    {{ trans('help.custom_css_guideline') }}
  </p>

  <pre>
  .primary-nav {
    background: #020428;
  }
  .search-box-button button {
    background: #404041;
  }
  .footer {
    background: #020428;
  }
</pre>
@endif
