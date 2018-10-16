<?php

namespace EatWhat;

/**
 * Api Base
 * 
 */
class EatWhatBase 
{
    /**
     * static classname
     * 
     */
    public static function className($withoutNamespace = false)
    {
        $classname = get_called_class();
        $withoutNamespace && ($classname = substr($classname, (strrpos($classname, "\\") + 1)));
        return $classname;
    }
}