<?php

namespace WebEdit\Form;

use WebEdit\Application;

final class Extension extends Application\Extension {

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        $builder->addDefinition('form')
                ->setImplement('WebEdit\Form\Factory');
    }

}
