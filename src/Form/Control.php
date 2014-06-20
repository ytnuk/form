<?php

namespace WebEdit\Form;

use WebEdit\Application;

abstract class Control extends Application\Control {

    protected $form;

    protected function createComponentForm() {
        return $this->form->create();
    }

}
