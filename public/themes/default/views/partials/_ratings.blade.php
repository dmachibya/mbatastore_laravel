<ul>
  @for ($i = 0; $i < 5; $i++)
    @if ($ratings - $i >= 1)
      <li><a href="#" aria-label="Rated {{ $i + 1 }} out of 5"><i class="fas fa-star"></i></a></li>
    @elseif($ratings - $i < 1 && $ratings - $i > 0)
      <li><a href="#" aria-label="Rated {{ $i + 1 }} out of 5"><i class="fas fa-star-half-alt"></i></a></li>
    @else
      <li><a href="#" aria-label="Rated {{ $i + 1 }} out of 5"><i class="far fa-star"></i></a></li>
    @endif
  @endfor
</ul>
