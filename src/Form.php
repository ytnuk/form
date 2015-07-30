<?php
namespace Ytnuk;

use Nette;

/**
 * Class Form
 *
 * @package Ytnuk\Form
 */
abstract class Form
	extends Nette\Application\UI\Form
{

	/**
	 * @param string $message
	 * @param string $type
	 *
	 * @return \stdClass
	 */
	public function flashMessage(
		$message,
		$type = 'info'
	) {
		$control = $this->getControl();

		return $control->flashMessage(
			$message,
			$type
		);
	}

	/**
	 * @inheritdoc
	 */
	protected function attached($control)
	{
		parent::attached($control);
		if ( ! is_array($this->onSuccess)) {
			$this->onSuccess = [];
		}
		array_unshift(
			$this->onSuccess,
			function () {
				$this->flashMessage(
					$this->formatFlashMessage('success'),
					'success'
				);
			}
		);
		$this->onSuccess[] = function () {
			if ( ! $this->presenter->isAjax()) {
				$this->getControl()->redirect('this');
			}
		};
		if ( ! is_array($this->onError)) {
			$this->onError = [];
		}
		array_unshift(
			$this->onError,
			function () {
				$this->flashMessage(
					$this->formatFlashMessage('error'),
					'danger'
				);
			}
		);
		if ( ! is_array($this->onSubmit)) {
			$this->onSubmit = [];
		}
		array_unshift(
			$this->onSubmit,
			function () {
				if ($this->presenter->isAjax()) {
					$this->getControl()->redrawControl();
				}
			}
		);
	}

	/**
	 * @return Nette\Application\UI\Control
	 */
	protected function getControl()
	{
		return $this->lookup(Nette\Application\UI\Control::class);
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	protected function formatFlashMessage($type)
	{
		$message = [
			'form',
		];
		$button = $this->isSubmitted();
		if ($button instanceof Nette\Forms\Controls\Button && $path = $button->lookupPath(
				self::class,
				FALSE
			)
		) {
			$message = array_merge(
				$message,
				explode(
					'-',
					$path
				)
			);
		}

		return implode(
			'.',
			array_merge(
				$message,
				[
					$type,
					'message',
				]
			)
		);
	}
}
