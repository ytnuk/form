<?php

namespace WebEdit\Form;

use Nette\Forms;
use WebEdit;
use WebEdit\Form;

abstract class Container extends Forms\Container
{

    public function __construct()
    {
        $this->monitor(Form::class);
    }

    protected function attached($form)
    {
        parent::attached($form);
        if ($form instanceof Form) {
            $this->currentGroup = $this->form->currentGroup;
            $this->configure();
        }
    }

    abstract protected function configure();
}
