<?php

namespace WebEdit\Form\Control;

use WebEdit\Form;

interface Factory {

    /**
     * @return Form\Control
     */
    public function create();
}
