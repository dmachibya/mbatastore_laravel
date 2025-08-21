@isset($categories)
  <div class="category-filters-section">
    <h3>
      <i class="fas fa-angle-left"></i>

      @if (Request::is('search'))
        <a class="link-filter-opt" data-name="insubgrp" data-value="all">
        @else
          <a href="{{ route('categories') }}">
      @endif

      @lang('theme.all_categories')</a>
    </h3>

    <ul class="cateogry-filters-list">
      @if (Request::is('search'))
        <li>
          @if (Request::has('ingrp'))
            <h4>{{ $category->name }}</h4>
            @php
              $t_categories = $products
                  ->pluck('product.categories')
                  ->flatten()
                  ->unique();
              $t_categories = $t_categories
                  ->pluck('subGroup.slug')
                  ->flatten()
                  ->unique()
                  ->toArray();
            @endphp

            <ul>
              @foreach ($category->subGroups as $slug => $category)
                @if (in_array($category->slug, $t_categories))
                  <li>
                    <a class="link-filter-opt" data-name="insubgrp" data-value="{{ $category->slug }}">
                      {{ $category->name }}
                    </a>
                  </li>
                @endif
              @endforeach
            </ul>
          @elseif(Request::has('insubgrp') && Request::get('insubgrp') != 'all')
            @php
              $t_categories = $products
                  ->pluck('product.categories')
                  ->flatten()
                  ->unique();
              $t_categories = $t_categories
                  ->pluck('slug')
                  ->flatten()
                  ->unique()
                  ->toArray();
            @endphp

            <h4>
              <i class="fas fa-angle-left"></i>
              <a class="link-filter-opt" data-name="ingrp" data-value="{{ $category->group->slug }}">
                {{ $category->group->name }}
              </a>
            </h4>
            <h4>
              <i class="fas fa-angle-left"></i>
              <a class="link-filter-opt" data-name="ingrp" data-value="{{ $category->slug }}">
                {{ $category->name }}
              </a>
            </h4>

            <ul>
              @foreach ($category->categories as $category)
                @if (in_array($category->slug, $t_categories))
                  <li>
                    <a class="link-filter-opt" data-name="in" data-value="{{ $category->slug }}">
                      {{ $category->name }}
                    </a>
                  </li>
                @endif
              @endforeach
            </ul>
          @elseif(Request::has('in'))
            <h4>
              <i class="fas fa-angle-left"></i>
              <a class="link-filter-opt" data-name="ingrp" data-value="{{ $category->subGroup->group->slug }}">
                {{ $category->subGroup->group->name }}
              </a>
            </h4>
            <h4>
              <i class="fas fa-angle-left"></i>
              <a class="link-filter-opt" data-name="insubgrp" data-value="{{ $category->subGroup->slug }}">
                {{ $category->subGroup->name }}
              </a>
            </h4>

            <ul>
              <li>{{ $category->name }}</li>
            </ul>
          @else
            @php
              $t_categories = $products
                  ->pluck('product.categories')
                  ->flatten()
                  ->unique();
              $t_categories = $t_categories
                  ->pluck('subGroup.group')
                  ->flatten()
                  ->unique();
            @endphp

            @foreach ($t_categories as $category)
        <li>
          <a class="link-filter-opt" data-name="ingrp" data-value="{{ $category->slug }}">
            {{ $category->name }}
          </a>
        </li>
      @endforeach
      @endif
      </li>
    @elseif(Request::is('categorygrp/*'))
      <li>
        <h4><i class="fas fa-angle-right"></i> {{ $categoryGroup->name }}</h4>

        <ul>
          @foreach ($categoryGroup->subGroups as $slug => $t_category)
            {{-- @if ($t_category->categories->count()) --}}
            <li><a href="{{ route('categories.browse', $t_category->slug) }}">{{ $t_category->name }}</a></li>
            {{-- @endif --}}
          @endforeach
        </ul>
      </li>
    @elseif(Request::is('categories/*'))
      <li>
        <h4>
          <i class="fas fa-angle-left"></i>
          <a href="{{ route('categoryGrp.browse', $categorySubGroup->group->slug) }}">
            {{ $categorySubGroup->group->name }}
          </a>
        </h4>

        <h4><i class="fas fa-angle-right"></i> {{ $categorySubGroup->name }}</h4>

        <ul>
          @foreach ($categorySubGroup->categories as $slug => $t_category)
            <li><a href="{{ route('category.browse', $t_category->slug) }}">{{ $t_category->name }}</a></li>
          @endforeach
        </ul>
      </li>
    @elseif(Request::is('category/*'))
      <li>
        <h4>
          <i class="fas fa-angle-left"></i>
          <a href="{{ route('categoryGrp.browse', $category->subGroup->group->slug) }}">
            {{ $category->subGroup->group->name }}
          </a>
        </h4>

        <h4>
          <i class="fas fa-angle-left"></i>
          <a href="{{ route('categories.browse', $category->subGroup->slug) }}">
            {{ $category->subGroup->name }}
          </a>
        </h4>

        <ul>
          @foreach ($category->subGroup->categories as $t_category)
            <li>
              @if ($t_category->slug == $category->slug)
                <strong>{{ $t_category->name }}</strong>
              @else
                <a href="{{ route('category.browse', $t_category->slug) }}">{{ $t_category->name }}</a>
              @endif
            </li>
          @endforeach
        </ul>
      </li>
    @else
      @foreach ($categories as $slug => $t_category)
        <li>
          <a href="{{ route('category.browse', $t_category->slug) }}">{{ $t_category->name }}
            {{-- <span class="small">({{ $t_category->listings_count }})</span> --}}
          </a>
        </li>
      @endforeach
      @endif
    </ul>
  </div>
@endisset
