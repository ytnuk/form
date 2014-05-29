<?php

namespace WebEdit;

use Nette\Application;

final class Form extends Application\UI\Form {

    public function __construct(Translation\Translator $translator) {
        $this->setTranslator($translator);
        $this->setRenderer(new Form\Renderer);
    }

    public function getValues($asArray = TRUE) {
        $values = parent::getValues($asArray);
        if ($asArray) {
            $this->nullEmptyValues($values);
        }
        return $values;
    }

    private function nullEmptyValues(array &$values) {
        foreach ($values as $key => &$value) {
            if ($value === '') {
                $values[$key] = NULL;
            } elseif (is_array($value)) {
                $this->nullEmptyValues($value);
            }
        }
    }

}
