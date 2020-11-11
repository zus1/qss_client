<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseController extends AbstractController {

    protected function makeValidation(ValidatorInterface $validator, $entity, string $name, $value) {
        $failed = $validator->validatePropertyValue($entity, $name, $value);
        if($failed->count() > 0) {
            throw new Exception($failed->get(0)->getMessage());
        }
    }

}