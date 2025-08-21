<!-- CONTENT SECTION -->
<section class="mt-3">
  <div class="container mb-5">
    <div class="row row-col-border" data-gutter="60">
      <div class="col-md-9">
        <article class="blog-post">
          @if ($blog->image)
            <img class="w-100 lazy" src="{{ get_storage_file_url(optional($blog->coverImage)->path, 'tiny') }}" data-src="{{ get_storage_file_url(optional($blog->coverImage)->path, 'full') }}" alt="{{ $blog->title }}" title="{{ $blog->title }}" />
          @endif

          <h1 class="blog-post-title">{{ $blog->title }}</h1>

          <ul class="blog-post-meta">
            <li>{{ trans('theme.published_at') . ' ' . $blog->published_at->diffForHumans() }}</li>
            <li>{{ trans('theme.by') }} <a href="{{ route('blog.author', $blog->user_id) }}">{!! $blog->author->getName() !!}</a>
            </li>
          </ul>

          <p class="blog-post-body">
            {!! $blog->content !!}
          </p>
        </article>
      </div> <!-- /.col-md-9 -->

      <div class="col-md-3">
        @include('theme::partials._blog_sidebar')
      </div> <!-- /.col-md-3 -->
    </div>
  </div><!-- /.container -->
</section>
