<?php


namespace Bunny\Admin\Filter;

use BunnyPHP\BunnyPHP;
use BunnyPHP\Config;
use BunnyPHP\Filter;

class AuthFilter extends Filter
{
    public function doFilter($fa = [])
    {
        /**
         * @var $filter Filter
         */
        $filterClass = Config::load('bunny_php_admin')->get('filter');
        if ($filterClass) {
            $filterName = BunnyPHP::getClassName($filterClass, 'filter');
            $filter = new $filterName($this->_mode);
            return $filter->doFilter($fa);
        } else {
            return self::NEXT;
        }
    }
}