<?php

namespace Antares\Crud;

use Illuminate\Support\MessageBag;

class CrudMessageBag extends MessageBag
{
    /**
     * Clear messages array
     *
     * @return $this
     */
    public function clear()
    {
        $this->messages = [];
        return $this;
    }

    /**
     * Reset messages errors bag
     *
     * @param string $format
     * @return $this
     */
    public function reset($format = ':message')
    {
        $this->clear();
        $this->format = $format;
        return $this;
    }

    /**
     * Add an unnamed error item
     *
     * @param string $error
     * @return $this
     */
    public function addUnnamed(string $error)
    {
        return $this->add('__unnamed__', $error);
    }
}
