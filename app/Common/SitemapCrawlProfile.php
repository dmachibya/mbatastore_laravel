<?php

namespace App\Common;

use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlProfiles\CrawlProfile;

class SitemapCrawlProfile extends CrawlProfile
{
  protected $profile;

  public function shouldCrawlCallback(callable $callback)
  {
    $this->profile = $callback;
  }

  /*
  * Determine if the given url should be crawled.
  */
  public function shouldCrawl(UriInterface $url): bool
  {
    $path = $url->getPath();

    if (
      str_contains($path, "product") ||
      str_contains($path, "category") ||
      str_contains($path, "categories") ||
      str_contains($path, "categorygrp") ||
      str_contains($path, "brand") ||
      str_contains($path, "events") ||
      str_contains($path, "event") ||
      str_contains($path, "blog") ||
      str_contains($path, "page") ||
      str_contains($path, "search") ||
      str_contains($path, "customer") ||
      str_contains($path, "selling")
    ) {
      return ($this->profile)($url);
    }

    return false;
  }
}
