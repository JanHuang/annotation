<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Annotation\Types;

class Variable extends Types
{
    public function syntax()
    {
        return '/\@(?P<name>\w+)\s+(?P<params>.*?)/';
    }
}