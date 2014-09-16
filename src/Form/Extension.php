<?php

namespace WebEdit\Form;

use WebEdit\Application;
use WebEdit\Form;
use WebEdit\Module;
use WebEdit\Translation;

final class Extension extends Module\Extension implements Translation\Provider, Application\Provider
{

    protected $resources = [
        'renderer' => 'WebEdit\Form\Renderer'
    ];

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        $builder->getDefinition($this->prefix('factory'))
            ->addSetup('setTranslator', [$builder->getDefinition('translation.default')]);
    }

    public function getApplicationResources()
    {
        return [
            'services' => [
                $this->prefix('factory') => [
                    'class' => Form\Factory::class,
                    'setup' => [
                        'setRenderer' => [$this->resources['renderer']],
                    ]
                ]
            ]
        ];
    }

}
