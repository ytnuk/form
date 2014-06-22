<?php

namespace WebEdit\Form;

use WebEdit\Application;

final class Extension extends Application\Extension {

    private $defaults = [
        'renderer' => 'WebEdit\Form\Renderer'
    ];

    public function loadConfiguration() {
        $config = $this->getConfig($this->defaults);
        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix('factory'))
                ->setImplement('WebEdit\Form\Factory')
                ->addSetup('setRenderer', [new $config['renderer']]);
    }

    public function beforeCompile() {
        $builder = $this->getContainerBuilder();
        $builder->getDefinition($this->prefix('factory'))
                ->addSetup('setTranslator', [$builder->getDefinition('translation.default')]);
    }

}
