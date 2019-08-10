<?php

namespace OrpheusAppBundle\Utils;

use Symfony\Component\Validator\ConstraintViolationList;

class ViolationsExtractor
{
    public static function extract(ConstraintViolationList $violationsList)
    {
        $output = [];
        foreach ($violationsList as $violation) {
            $output[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $output;
    }

}