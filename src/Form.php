<?php

namespace Ytnuk;

use Nette;

/**
 * Class Form
 *
 * @package Ytnuk\Form
 */
abstract class Form extends Nette\Application\UI\Form
{

	/**
	 * @param Nette\Application\UI\Control $control
	 */
	protected function attached($control)
	{
		parent::attached($control);
		if ( ! is_array($this->onSuccess)) {
			$this->onSuccess = [];
		}
		array_unshift($this->onSuccess, function () {
			$this->flashMessage($this->formatFlashMessage('success'), 'success');
		});
		if ( ! is_array($this->onError)) {
			$this->onError = [];
		}
		array_unshift($this->onError, function () {
			$this->flashMessage($this->formatFlashMessage('error'), 'danger');
		});
	}

	/**
	 * @param $message
	 * @param string $type
	 * @param Nette\Application\UI\Control $control
	 *
	 * @return \stdClass
	 */
	public function flashMessage($message, $type = 'info', Nette\Application\UI\Control $control = NULL)
	{
		if ( ! $control) {
			$control = $this->lookup(Nette\Application\UI\Control::class);
		}

		return $control->flashMessage($message, $type);
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	protected function formatFlashMessage($type)
	{
		$message = [
			'form'
		];
		if ($button = $this->isSubmitted()) {
			$message = array_merge($message, explode('-', $button->lookupPath(self::class)));
		}

		return implode('.', array_merge($message, [
			$type,
			'message'
		]));
	}
}
