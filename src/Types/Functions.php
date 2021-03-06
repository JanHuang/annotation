<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Annotation\Types;
use FastD\Annotation\Interfaces\TypesInterface;

/**
 * Class Directive
 * @package FastD\Annotation\Types
 */
class Functions implements TypesInterface
{
    /**
     * @return string
     */
    public function syntax()
    {
        return '/\@(\w+)\((.*)\)/';
    }

    /**
     * @param $docComment
     * @return array
     */
    public function parse($docComment)
    {
        $pattern = $this->syntax();

        $params = [];

        if (preg_match_all($pattern, $docComment, $match)) {
            if (!isset($match[1])) {
                return [];
            }

            foreach ($match[1] as $key => $value) {
                $info = explode(',', $match[2][$key]);
                $args = [];
                array_map(function ($v) use (&$args) {
                    if (false !== strpos($v, '=')) {
                        list($index, $item) = explode('=', $v);
                        $json = json_decode($item, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $item = $json;
                        }
                        $args[trim($index)] = $item;
                    } else {
                        $args[] = trim(str_replace(['"', "'"], '', $v));
                    }
                }, $info);
                $params[$value] = $args;
            }
            unset($args);
        }

        return $params;
    }
}
