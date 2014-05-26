<?php

namespace WebEdit;

use Nette\Application;
use WebEdit\Form;

final class Form extends Application\UI\Form {

    private $entity;

    public function __construct(Translation\Translator $translator) {
        $this->setTranslator($translator);
        $this->setRenderer(new Form\Renderer);
        $this->monitor('WebEdit\Form\Control');
    }

    public function setEntity($entity) {
        $this->entity = $entity;
    }

    protected function attached($control) {
        parent::attached($control);
        if ($control instanceof Form\Control) {
            $this->onSubmit[] = [$control, 'handle' . ($this->entity ? 'Edit' : 'Add')];
            if ($this->entity) {
                $this->addSubmit('edit', 'form.button.save')->setAttribute('class', 'btn btn-warning');
                $this->addSubmit('delete', 'form.button.delete')->setValidationScope(FALSE)->setAttribute('class', 'btn btn-danger');
            } else {
                $this->addSubmit('add', 'form.button.add');
            }
        }
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
