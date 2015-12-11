<?php

namespace Jam\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsProfanity extends Constraint
{
    public $message = 'The string "%string%" contains profanity. Please change it.';
}