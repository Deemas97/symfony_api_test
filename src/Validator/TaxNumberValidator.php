<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TaxNumberValidator extends ConstraintValidator
{
    private const PATTERNS = [
        'DE' => '/^DE\d{9}$/',
        'IT' => '/^IT\d{11}$/',
        'GR' => '/^GR\d{9}$/',
        'FR' => '/^FR[A-Z]{2}\d{9}$/',
    ];

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var TaxNumber $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        foreach (self::PATTERNS as $country => $pattern) {
            if (preg_match($pattern, $value)) {
                return;
            }
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation()
        ;
    }
}
