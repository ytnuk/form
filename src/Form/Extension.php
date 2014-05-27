<?php

namespace WebEdit\Form;

use WebEdit;

final class Extension extends WebEdit\Extension {

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        $builder->addDefinition('form')
                ->setImplement('WebEdit\Form\Factory');
    }

}
