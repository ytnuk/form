<?php

namespace WebEdit\Form;

use Nette\Forms;
use WebEdit\Form;
use WebEdit;

abstract class Container extends Forms\Container {

    public function __construct() {
        $this->monitor('WebEdit\Form');
    }

    protected function attached($form) {
        parent::attached($form);
        if ($form instanceof Form) {
            $reflection = new WebEdit\Reflection($this);
            $form->addGroup($reflection->getModuleName('group'));
            $this->currentGroup = $this->form->currentGroup;
            $this->configure();
        }
    }

    abstract protected function configure();
}
