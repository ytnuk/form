<?php

namespace WebEdit\Form;

use Nextras\Forms;
use WebEdit\Application;
use WebEdit\Form;
use WebEdit\Module;
use WebEdit\Translation;

final class Extension extends Module\Extension implements Translation\Provider, Application\Provider
{

    public function getResources()
    {
        return [
            'renderer' => Forms\Rendering\Bs3FormRenderer::class
        ];
    }

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
                $this->prefix('renderer') => $this['renderer'],
                $this->prefix('factory') => [
                    'class' => Form\Factory::class,
                    'setup' => [
                        'setRenderer' => [$this->prefix('renderer', TRUE)],
                    ],
                    'parameters' => ['entity']
                ]
            ]
        ];
    }

}
