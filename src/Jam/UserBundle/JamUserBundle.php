<?php

namespace Jam\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class JamUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
