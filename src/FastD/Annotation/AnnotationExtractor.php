<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/4/1
 * Time: 下午11:06
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Annotation;

/**
 * Class AnnotationExtractor
 *
 * @package FastD\Annotation
 */
class AnnotationExtractor extends \ReflectionClass
{
    /**
     * @var array
     */
    private $methodsAnnotation;

    /**
     * @var string
     */
    private $separator = '@';

    /**
     * Get class and method annotation.
     *
     * @return array
     */
    public function getAnnotation()
    {
        return array(
            'class'     => $this->getClassAnnotation(),
            'methods'   => $this->getMethodsAnnotation(),
        );
    }

    /**
     * Get class annotation.
     *
     * @return string
     */
    public function getClassAnnotation()
    {
        return $this->getDocComment();
    }

    /**
     * @return array
     */
    public function getMethodsAnnotation()
    {
        if (null === $this->methodsAnnotation) {
            foreach ($this->getMethods() as $method) {
                $this->methodsAnnotation[$method->getName()] = $method->getDocComment();
            }
        }

        return $this->methodsAnnotation;
    }

    /**
     * @param $method
     * @return string
     */
    public function getMethodAnnotation($method)
    {
        return $this->getMethodsAnnotation()[$method];
    }

    /**
     * @param $annotation
     * @param $keyword
     * @return array
     */
    public function getParameters($annotation, $keyword)
    {
        if (!$this->hasKeyword($annotation, $keyword)) {
            return array();
        }

        $pattern = sprintf('/\%s%s\((?P<params>.*?)\)/', $this->separator, $keyword);

        $parameters = array();

        if (preg_match_all($pattern, str_replace(array("\r\n", "\n", '*'), '', $annotation), $match)) {

            $match['params'] = preg_split('/(?<=\"|\]|\}),\s*(?=\w)/', str_replace('\\', '\\\\', implode(',', $match['params'])));

            foreach ($match['params'] as $key => $value) {
                if (false !== strpos($value, '=')) {
                    list($key, $value) = explode('=', $value);
                    if (false !== ($json = json_decode($value, true))) {
                        $value = $json;
                        unset($json);
                    }
                } else {
                    $value = trim($value, '"');
                }

                $parameters[$key] = $value;
            }
        }

        return $parameters;
    }

    /**
     * @param $annotation
     * @param $keyword
     * @return bool
     */
    public function hasKeyword($annotation, $keyword)
    {
        return false !== strpos($annotation, $keyword);
    }

    /**
     * @param $class
     * @return AnnotationExtractor
     */
    public static function getExtractor($class)
    {
        return new static($class);
    }
}