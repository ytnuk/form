<?php
namespace Ytnuk\Form;

use Kdyby;
use Nette;
use Nextras;
use ReflectionProperty;

final class Renderer
	extends Nextras\Forms\Rendering\Bs3FormRenderer
{

	private $groups = [];

	private $containers = [];

	public function renderBody()
	{
		$groupsProperty = new ReflectionProperty(
			Nette\Forms\Form::class,
			'groups'
		);
		$groupsProperty->setAccessible(TRUE);
		$groups = $groupsProperty->getValue($this->form);
		$this->groups = $this->containers = [];
		$groupsProperty->setValue(
			$this->form,
			array_filter(
				$groups,
				function (
					Nette\Forms\ControlGroup $group,
					$key
				) {
					if ( ! $group->getControls() || ! $group->getOption('visual')) {
						return FALSE;
					}
					$this->groups[$key] = [
						'group' => $group,
						'body' => $body = $this->renderControls($group),
						'key' => $key,
					];

					return $body;
				},
				ARRAY_FILTER_USE_BOTH
			)
		);
		$body = parent::renderBody();
		$groupsProperty->setValue(
			$this->form,
			$groups
		);

		return $body;
	}

	public function renderControls($parent)
	{
		if ($parent instanceof Nette\Forms\ControlGroup) {
			$key = array_search(
				$parent,
				array_column(
					$this->groups,
					'group',
					'key'
				)
			);
			if ($key !== FALSE) {
				return $this->groups[$key]['body'];
			}

			return implode(
				NULL,
				array_map(
					function (Nette\Forms\Container $container) {
						$renderer = $container;
						do {
							$parent = $renderer->lookup(
								Nette\Forms\IFormRenderer::class,
								FALSE
							);
							if ($parent) {
								$renderer = $parent;
							}
						} while ($parent);
						if ($renderer instanceof Nette\Forms\IFormRenderer) {
							$path = $renderer->lookupPath(
								NULL,
								FALSE
							);
							if ($path) {
								$rendered = array_key_exists(
									$path,
									$this->containers
								);
								if ( ! $rendered) {
									$this->containers[$path] = NULL;

									return $this->containers[$path] = $renderer->render($this->form);
								}
							}

							return NULL;
						}
						if ($container instanceof Kdyby\Replicator\Container) {
							$container = (new Nette\Forms\ControlGroup)->add(
								...
								array_values(
									iterator_to_array(
										$container->getComponents(
											FALSE,
											Nette\Forms\IControl::class
										)
									)
								)
							);
						}

						return parent::renderControls($container);
					},
					array_filter(
						$containers = array_filter(
							array_filter(
								array_map(
									function (Nette\ComponentModel\Component $component) {
										return $component->lookup(
											Nette\Forms\Container::class,
											FALSE
										);
									},
									$parent->getControls()
								)
							),
							function (Nette\Forms\Container $container) use
							(
								$parent
							) {
								return $container->getCurrentGroup() === $parent;
							}
						),
						function (
							Nette\Forms\Container $container,
							$key
						) use
						(
							$containers
						) {
							return $key === current(
								array_keys(
									$containers,
									$container
								)
							);
						},
						ARRAY_FILTER_USE_BOTH
					)
				)
			);
		}

		return parent::renderControls($parent);
	}
}
