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
        print_r($this->request->getUserData());
    }
}