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
	protected function attached($control) //TODO: burn this shit
	{
		if ( ! is_array($this->onError)) {
			$this->onError = [];
		}
		if ( ! is_array($this->onSuccess)) {
			$this->onSuccess = [];
		}
		array_unshift($this->onError, function () {
			$this->message('warning');
		});
		array_unshift($this->onSuccess, function () {
			$this->message('success');
		});
		parent::attached($control);
	}

	public function message($type)
	{
		//TODO: use Flash/Message storage when available
		//TODO: always use $this->parent control for flashMessage, when control is not available after redirecting, show those flashMessages at presenter
		$this->getParent()->flashMessage($this->formatMessage($type), $type);
	}

	/**
	 * @return string
	 */
	protected function formatMessage($type) //TODO: use onSuccess to success messages and onError for error messages
	{
		$message = 'form.action';
		if ($button = $this->isSubmitted()) {
			$message .= '.' . $button->getName();
		}
		$message .= '.' . $type . '.message';

		return $message;
	}
}
