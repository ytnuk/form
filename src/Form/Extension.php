<?php
namespace Ytnuk\Form;

use Kdyby;
use Nette;
use Ytnuk;

final class Extension
	extends Nette\DI\CompilerExtension
	implements Kdyby\Translation\DI\ITranslationProvider
{

	/**
	 * @var array
	 */
	private $defaults = [
		'renderer' => [
			'class' => Renderer::class,
			'wrappers' => [],
		],
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
		$renderer = $builder->addDefinition($this->prefix('renderer'))->setClass($this->config['renderer']['class']);
		if (is_a(
			$this->config['renderer'],
			Nette\Forms\Rendering\DefaultFormRenderer::class,
			TRUE
		)) {
			$renderer->addSetup(
				'$service->wrappers = array_merge_recursive($service->wrappers, ?)',
				[
					$this->config['renderer']['wrappers'],
				]
			);
		}
	}

	public function getTranslationResources() : array
	{
		return [
			__DIR__ . '/../../locale',
		];
	}
}
