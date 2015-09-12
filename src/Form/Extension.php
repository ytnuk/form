<?php
namespace Ytnuk\Form;

use Kdyby;
use Nette;
use Nextras;
use Ytnuk;

final class Extension
	extends Nette\DI\CompilerExtension
	implements Ytnuk\Config\Provider
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
		$config = $this->getConfig($this->defaults);
		foreach (
			$config['forms'] as $form
		) {
			$builder->getDefinition($form)->addSetup(
				'setTranslator',
				[$translator]
			)->addSetup(
				'setRenderer',
				['@' . $this->prefix('renderer')]
			);
		}
	}

	public function getConfigResources() : array
	{
		$config = $this->getConfig($this->defaults);

		return [
			'services' => [
				$this->prefix('renderer') => $config['renderer'],
			],
			Kdyby\Translation\DI\TranslationExtension::class => [
				'dirs' => [
					__DIR__ . '/../../locale',
				],
			],
			Kdyby\Replicator\DI\ReplicatorExtension::class => [],
			Nextras\Forms\DI\FormsExtension::class => [],
		];
	}
}
