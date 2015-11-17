<?php
namespace Ytnuk;

use Nette;
use stdClass;

abstract class Form
	extends Nette\Application\UI\Form
{

	public function flashMessage(
		string $message,
		string $type = 'info'
	) : stdClass
	{
		$control = $this->getControl();

		return $control->flashMessage(
			$message,
			$type
		);
	}

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
			if ( ! $this->getPresenter()->isAjax()) {
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
				$this->getControl()->redrawControl();
			}
		);
	}

	protected function getControl() : Nette\Application\UI\Control
	{
		return $this->lookup(Nette\Application\UI\Control::class);
	}

	protected function formatFlashMessage(string $type) : string
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
