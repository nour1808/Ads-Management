<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        if ($date === null) {
            //die('0');
            return '';
        }
        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenshDate)
    {
        if ($frenshDate === null) {
            die('1');
            throw new TransformationFailedException();
        }

        $date = \DateTime::createFromFormat('d/m/Y', $frenshDate);

        if ($date === null) {
            //die('2');
            throw new TransformationFailedException();
        }

        return $date;
    }
}