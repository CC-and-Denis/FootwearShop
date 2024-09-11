<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

class FormErrorHandler
{
    public function getAllFormErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true, false) as $error) {
            $formName = $error->getOrigin()->getName();
            $errors[] = [
                'field' => $formName,
                'message' => $error->getMessage(),
            ];
        }

        return $errors;
    }
}

?>