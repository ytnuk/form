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

	public function message()
	{
		//TODO: use Flash/Message storage when available
		//TODO: always use $this->parent control for flashMessage, when control is not available after redirecting, show those flashMessages at presenter
		$this->getParent()->flashMessage($this->formatMessage(), $this->getMessageType());
	}

	/**
	 * @return string
	 */
	protected function formatMessage() //TODO: use onSuccess to success messages and onError for error messages
	{
		$message = 'form.action';
		if ($button = $this->isSubmitted()) {
			$message .= '.' . $button->getName();
		}
		if ($type = $this->getMessageType()) {
			$message .= '.' . $type;
		}
		$message .= '.message';

		return $message;
	}

	/**
	 * @return string
	 */
	protected function getMessageType()
	{
		return $this->isValid() ? 'success' : 'warning';
	}

	/**
	 * @param Nette\Application\UI\Control $control
	 */
	protected function attached($control) //TODO: burn this shit
	{
		if ( ! is_array($this->onSubmit)) {
			$this->onSubmit = [];
		}
		array_unshift($this->onSubmit, [
			$this,
			'message'
		]);
		parent::attached($control);
	}
}
