<?php

class Joobsbox_Iterator_Generic extends ArrayObject
{
    public function get($index)
    {
        if ($this->offsetExists($index)) {
            return $this->offsetGet($index);
        } else {
            return false;
        }
    }
}