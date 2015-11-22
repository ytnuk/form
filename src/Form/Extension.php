<?php
namespace Ytnuk\Form;

use Kdyby;
use Nette;
use Nextras;
use Ytnuk;

final class Extension
	extends Nette\DI\CompilerExtension
	implements Kdyby\Translation\DI\ITranslationProvider
{

	/**
	 * @var array
	 */
	private $defaults = [
		'renderer' => Nextras\Forms\Rendering\Bs3FormRenderer::class,
		'forms' => [],
	];

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$translator = $builder->getDefinition($builder->getByType(Nette\Localization\ITranslator::class));
		foreach (
			$this->config['forms'] as $key => $class
		) {
			$form = $builder->addDefinition($this->prefix('form.' . $key))->setImplement($class);
			if ($translator) {
				$form->addSetup(
					'setTranslator',
					[$translator]
				);
			}
			$form->addSetup(
				'setRenderer',
				[$this->prefix('@renderer')]
			);
		}
	}

	public function loadConfiguration()
	{
		parent::loadConfiguration();
		$this->validateConfig($this->defaults);
		$providers = $this->compiler->getExtensions(Provider::class);
		array_walk(
			$providers,
			function (Provider $provider) {
				$this->config = $this->validateConfig(
					$this->config,
					$provider->getFormResources()
				);
			}
		);
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('renderer'))->setClass($this->config['renderer']);
	}

	public function getTranslationResources() : array
	{
		return [
			__DIR__ . '/../../locale',
		];
	}
}
