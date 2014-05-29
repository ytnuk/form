<?php

namespace WebEdit\Form;

use WebEdit;

abstract class Control extends WebEdit\Control {

    protected $form;

    protected function createComponentForm() {
        return $this->form->create();
    }

}
