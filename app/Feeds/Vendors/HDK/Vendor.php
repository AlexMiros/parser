<?php

namespace App\Feeds\Vendors\HDK;

use App\Feeds\Processor\SitemapHttpProcessor;
use App\Feeds\Utils\Link;
use App\Feeds\Feed\FeedItem;
use App\Feeds\Processor\HttpProcessor;

class Vendor extends SitemapHttpProcessor
{
    public array $first = [ 'https://hdkoenigpersonalcare.com/product-sitemap.xml' ];

    public function filterProductLinks( Link $link ): bool
    {
        return str_contains( $link->getUrl(), '/product/' );
    }
    
    public function isValidFeedItem( FeedItem $fi ): bool
    {
        return !empty( $fi->getMpn() ) && $fi->getCostToUs() > 0 && !empty( $fi->getProduct() ) && !empty( $fi->getImages() );
    }
    
}
