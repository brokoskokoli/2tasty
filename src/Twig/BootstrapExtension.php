<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BootstrapExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('bootstrap_carousel', [$this, 'getCarousel']),
        ];
    }


    public function getCarousel($images): string
    {
        if (count($images) == 0) {
            return '';
        }

        $result = '<style>

        /**
        CAROUSEL
         */
        .carousel-inner img {
          margin-left: auto;
          margin-right: auto;
        }
        .carousel-inner .item {
            background-size: cover;
            background-blend-mode: luminosity;
        }
        </style>';

        $result .= '<div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
            <!-- Indicators -->
            <ol class="carousel-indicators">';

        $first = true;
        foreach ($images as $index => $image) {
            $result .= '<li data-target="#myCarousel" data-slide-to="'. $index .'" class="'.($first?'active':'').'"></li>';
            $first = false;
        }

        $result .= '</ol>
            <!-- Wrapper for slides -->
            <div class="carousel-inner">';

        $first = true;
        foreach ($images as $index => $image) {
            $result .= '<div class="item '.($first?'active':'').'" style="background-image:url('.$image.');"><img src="'.$image.'">
                    </div>';
            $first = false;
        }

        $result .= '</div>
            <!-- Left and right controls -->
            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                <i class="fa fa-angle-left fa-3x center-icons"></i>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                <i class="fa fa-angle-right fa-3x center-icons"></i>
                <span class="sr-only">Next</span>
            </a>
        </div>';
        return $result;
    }
}
