<?php

namespace App\Feeds\Vendors\HDK;

use App\Feeds\Parser\HtmlParser;
use App\Feeds\Utils\ParserCrawler;
use App\Helpers\StringHelper;

class Parser extends HtmlParser
{
    public function getProduct(): string
    {
        return $this->getText( '.product_title' );
    }

    public function getMpn(): string
    {
        return $this->getText( '.product_sku' );
    }

    public function getCostToUs(): float
    {
        return StringHelper::getMoney( $this->getMoney( '.summary .price' ) );
    }

    public function getListPrice(): ?float
    {
        return null;
    }

    public function getShortDescription(): array
    {
        return [];
    }

    public function getDescription(): string
    {   
        return $this->getHtml( '.lead p' );
    }

    public function getBrand(): ?string
    {
        return null;
    }

    public function getUpc(): ?string
    {
        return null;
    }

    public function getCategories(): array
    {
        $html = $this->getHtml( '.posted_in' );
        
        $cats = (explode('tag', $html));
        array_shift($cats);
        foreach($cats as &$cat){
            $num1 = strpos($cat, '>');
            $cat = substr($cat, $num1+1);
            $num1 = strpos($cat, '<');
            $cat = substr($cat, 0, $num1);
        }

        return $cats;
    }

    public function getImages(): array
    {
        $html = $this->getText( "#woosvijs-js-extra" );
        
        $imgs = (explode('fullimg', $html));
        array_shift($imgs);
        foreach($imgs as &$img){
            $num1 = strpos($img, 'https:');
            $img = substr($img, $num1);
            $num1 = strpos($img, ',');
            $img = substr($img, 0, $num1-1);
        }
        
        return $imgs;
    }

    public function getWeight(): ?float
    {
        return 0;
    }

    public function getDimX(): ?float
    {
        $x = $this->getText( '.lead' );
        if (stristr($x, 'Large:')) {
            $num1 = strpos($x, 'Large:');
            $num2 = substr($x, $num1 + 7);
            $num1 = strpos($num2, ' x ');
            $num2 = substr($num2, 0, $num1);
    
            return StringHelper::getFloat($num2);
        }
        return null;
    }

    public function getDimY(): ?float
    {
        $x = $this->getText( '.lead' );
        if (stristr($x, 'Large:')) {
            $num1 = strpos($x, 'Large:');
            $num2 = substr($x, $num1 + 7);
            $num1 = strpos($num2, ' x ');
            $num2 = substr($num2, $num1 + 3);
            $num1 = strpos($num2, ' x ');
            $num2 = substr($num2, 0, $num1);

            return StringHelper::getFloat($num2);
        }
        return null;
    }

    public function getDimZ(): ?float
    {
        $x = $this->getText( '.lead' );
        if (stristr($x, 'Large:')) {
            $num1 = strpos($x, 'Large:');
            $num2 = substr($x, $num1 + 7);
            $num1 = strpos($num2, ' x ');
            $num2 = substr($num2, $num1 + 3);
            $num1 = strpos($num2, ' x ');
            $num2 = substr($num2, $num1 + 3);

            return StringHelper::getFloat($num2);
        }
        return null;
    }

    public function getShippingDimX(): ?float
    {
        return null;
    }

    public function getShippingDimY(): ?float
    {
        return null;
    }

    public function getShippingDimZ(): ?float
    {
        return null;
    }

    public function getShippingWeight(): ?float
    {
        return null;
    }

    public function getMinAmount(): int
    {
        $html = $this->getHtml( '.quantity' );
        $num1 = strpos($html, 'value');
        $html = substr($html, $num1 + 7);
        $num1 = strpos($html, 'title');
        $html = substr($html, 0, $num1 - 2);
        return StringHelper::getFloat($html) ?? 1;
    }

    public function getNewMapPrice(): ?float
    {
        return null;
    }

    public function getAvail(): ?int
    {
        $x = $this->getText( '.stock out-of-stock' );
        if (stristr($x, 'Out')) {
            return 0;
        }
        return self::DEFAULT_AVAIL_NUMBER;
    }

    public function getAttributes(): ?array
    {
        return null;
    }

    public function getOptions(): array
    {
        $options = [];
        $option_lists = $this->filter( '.variations' );

        if ( !$option_lists->count() ) {
            return $options;
        }

        $option_lists->each( function ( ParserCrawler $list ) use ( &$options ) {
            $label = $list->filter( 'select' );
            if ( $label->count() === 0 ) {
                return;
            }
            $name = $this->getText( '.variations label' );
            $options[ $name ] = [];
            $list->filter( 'option' )->each( function ( ParserCrawler $option ) use ( &$options, $name ) {
                $options[ $name ][] = trim( $option->text(), '  ' );
            } );
            //array_shift($options[ $name ]);
        } );

        return $options;
    }

    public function getVideos(): array
    {
        return [];
    }

    public function getProductFiles(): array
    {
        return [];
    }
}
