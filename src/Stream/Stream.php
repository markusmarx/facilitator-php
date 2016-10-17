<?php

namespace Facilitator\Stream;


/**
 * Base class for streams.
 *
 * @class      Stream
 * @package    Mxe\Facilitator\Stream
 */
class Stream
{
    /**
     * @var array
     */
    private $list;

    /**
     * Stream constructor.
     *
     * @param array $list list to manipulate.
     */
    public function __construct($list)
    {
        $this->list = $list;
    }

    /**
     * @param \Closure $closure
     */
    public function map(\Closure $closure)
    {
        $this->list = array_map($closure, $this->list);
    }

    /**
     * Returns stream from string.
     *
     * @param string $string   string to split
     * @param string $imploder rule to split string
     */
    static final public function fromString($string, $imploder)
    {

    }

    /**
     * Returns processed array.
     *
     * @return array
     */
    public function toList()
    {
        return $this->list;
    }


}