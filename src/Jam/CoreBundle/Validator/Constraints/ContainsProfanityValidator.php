<?php

namespace Jam\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Snipe\BanBuilder\CensorWords;

class ContainsProfanityValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $censor = new CensorWords;
        $string = $censor->censorString($value);

        if (count($string['matched']) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
}