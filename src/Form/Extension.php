<?php

namespace WebEdit\Form;

use Kdyby\Translation;
use Nette\DI;
use Nextras\Forms;
use WebEdit\Config;

/**
 * Class Extension
 *
 * @package WebEdit\Form
 */
final class Extension extends DI\CompilerExtension implements Config\Provider
{

	/**
	 * @var array
	 */
	private $defaults = [
		'renderer' => Forms\Rendering\Bs3FormRenderer::class,
		'forms' => []
	];

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$translator = $builder->getDefinition('translation.default');
		$config = $this->getConfig($this->defaults);
		foreach ($config['forms'] as $form) {
			$builder->getDefinition($form)
				->addSetup('setTranslator', [$translator])
				->addSetup('setRenderer', ['@' . $this->prefix('renderer')]);
		}
	}

	/**
	 * @return array
	 */
	public function getConfigResources()
	{
		return [
			'services' => [
				$this->prefix('renderer') => $this->defaults['renderer']
			],
			Translation\DI\TranslationExtension::class => [
				'dirs' => [
					__DIR__ . '/../../locale'
				]
			]
		];
	}
}
