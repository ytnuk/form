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
        $translator = $builder->getDefinition('translation.default');
        foreach ($this['factories'] as $name => $factory) {
            $builder->getDefinition($name)
                ->addSetup('setTranslator', [$translator]);
        }
    }

    public function getApplicationResources()
    {
        return [
            'services' => [
                    $this->prefix('renderer') => $this['renderer']
                ] + array_map(function ($factory) {
                    $factory['setup']['setRenderer'] = [$this->prefix('renderer', TRUE)];
                    return $factory;
                }, $this['factories'])
        ];
    }

}
