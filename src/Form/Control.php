<?php

namespace WebEdit\Form;

use WebEdit\Application;
use WebEdit\Form;

abstract class Control extends Application\Control
{

    /**
     * @var Form
     */
    protected $form;

    protected function createComponentForm()
    {
        return $this->form->create();
    }

}
