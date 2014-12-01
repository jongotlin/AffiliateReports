<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('price', [$this, 'priceFilter']),
        ];
    }

    /**
     * @param $number
     * @param int $decimals
     *
     * @return string
     */
    public function priceFilter($number, $decimals = 2)
    {
        //Hard space as thousand separator
        return number_format($number/100, $decimals, ',', ' ') . ':-';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'invoicer_extension';
    }
}
