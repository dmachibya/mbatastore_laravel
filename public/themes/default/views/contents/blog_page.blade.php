<!-- CONTENT SECTION -->
<section class="mt-3">
  <div class="container mb-5">
    <div class="row row-col-border" data-gutter="60">
      <div class="col-md-9">
        @forelse($blogs as $blog)
          <article class="blog-post mb-5">
            @if ($blog->image)
              <a href="{{ route('blog.show', $blog->slug) }}">
                <img class="lazy w-100" src="{{ get_storage_file_url(optional($blog->coverImage)->path, 'tiny') }}" data-src="{{ get_storage_file_url(optional($blog->coverImage)->path, 'blog') }}" alt="{{ $blog->title }}" title="{{ $blog->title }}" />
              </a>
            @endif

            <h1 class="blog-post-title"><a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a></h1>
            <p class="blog-post-excerpt">
              {!! \Illuminate\Support\Str::limit($blog->excerpt, 250) !!}
              <a class="pull-right btn btn-link" href="{{ route('blog.show', $blog->slug) }}">{{ trans('theme.button.read_more') }}</a>
            </p>

            <ul class="blog-post-meta">
              <li>{{ trans('theme.published_at') . ' ' . $blog->published_at->diffForHumans() }}</li>
              <li>by <a href="{{ route('blog.author', $blog->user_id) }}">{!! $blog->author->getName() !!}</a>
              </li>
            </ul>
          </article>
        @empty
          <h3 class="text-center text-muted mt-5">{{ trans('theme.notify.nothing_found') }}</h3>
        @endforelse

        <div class="text-center">
          {{ $blogs->links('theme::layouts.pagination') }}
        </div>
      </div> <!-- /.col-md-9 -->

      <div class="col-md-3">
        @include('theme::partials._blog_sidebar')
      </div> <!-- /.col-md-3 -->
    </div>
  </div><!-- /.container -->
</section>
