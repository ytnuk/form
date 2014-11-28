<?php

namespace Ytnuk\Form;

use Kdyby;
use Nette;
use Nextras;
use Ytnuk;

/**
 * Class Extension
 *
 * @package Ytnuk\Form
 */
final class Extension extends Nette\DI\CompilerExtension implements Ytnuk\Config\Provider
{

	/**
	 * @var array
	 */
	private $defaults = [
		'renderer' => Nextras\Forms\Rendering\Bs3FormRenderer::class,
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
		$config = $this->getConfig($this->defaults);

		return [
			'services' => [
				$this->prefix('renderer') => $config['renderer']
			],
			Kdyby\Translation\DI\TranslationExtension::class => [
				'dirs' => [
					__DIR__ . '/../../locale'
				]
			]
		];
	}
}
