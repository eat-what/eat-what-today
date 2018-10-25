<?php

namespace EatWhat\Traits;

/**
 * Eat Traits For Eat Api
 * 
 */
trait EatWhatTrait
{
    public function test()
    {
        var_dump(\EatWhat\EatWhatStatic::convertBase(4572, 62));
    }
}