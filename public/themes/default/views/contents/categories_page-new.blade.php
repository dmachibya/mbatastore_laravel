<section>
  <div id="all-categories-wrapper">
    <div class="container">
      <div class="row">
        @foreach ($all_categories as $categoryGroup)
          @if ($categoryGroup->subGroups->count())
            <div class="col-12 mb-5 pt-4 pb-3 category-grp-wrapper" @if ($categoryGroup->backgroundImage) style="background-image: url('{{ get_storage_file_url(optional($categoryGroup->backgroundImage)->path, 'full') }}')" @endif>
              {{-- @if ($categoryGroup->backgroundImage)
              <img class="mega-menu-background" src="{{ get_storage_file_url(optional($categoryGroup->backgroundImage)->path, 'full') }}" />
            @endif --}}

              <h2 class="mb-2">
                <a href="{{ route('categoryGrp.browse', $categoryGroup->slug) }}">
                  {{ Str::upper($categoryGroup->name) }}
                </a>
              </h2>

              <div class="row px-3">
                @foreach ($categoryGroup->subGroups as $subGroup)
                  <div class="col-6 col-md-4 col-lg-3 pl-1 pr-3 my-2">
                    <h3 class="nav-category-inner-title my-1">
                      <a href="{{ route('categories.browse', $subGroup->slug) }}">{{ $subGroup->name }}</a>
                    </h3>

                    <ul class="nav-category-inner-list show-hide-content less">
                      @foreach ($subGroup->categories as $cat)
                        <li>
                          <a href="{{ route('category.browse', $cat->slug) }}">{{ $cat->name }}</a>
                        </li>
                      @endforeach
                    </ul>

                    @if ($subGroup->categories->count() > 3)
                      <a href="javascript::void(0)" class="small show-hide-content-btn">
                        {{ trans('theme.show_more') }} <i class="fa fa-angle-down"></i>
                      </a>
                    @endif
                  </div><!-- /.col-3 -->
                @endforeach
              </div> <!-- /.row -->
            </div><!-- /.col-12 -->
          @endif
        @endforeach
      </div> <!-- /.row -->
    </div> <!-- /.container -->
  </div> <!-- /#all-categories-wrapper -->
</section>
