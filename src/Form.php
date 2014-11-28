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
		$this->getParentControl()
			->flashMessage($this->formatMessage(), $this->getMessageType());
	}

	protected function getParentControl()
	{
		return $this->lookup(Nette\Application\UI\Control::class);
	}

	protected function formatMessage()
	{
		$message = 'form.action';
		if ($this->submittedBy()) {
			$message .= '.' . $this->submitted->name;
		}
		if ($type = $this->getMessageType()) {
			$message .= '.' . $type;
		}
		$message .= '.message';

		return $message;
	}

	protected function submittedBy($name = TRUE)
	{
		$button = $this->submitted instanceof Nette\Forms\Controls\SubmitButton;
		if ($name === TRUE) {
			return $button;
		}

		return $button && $this->submitted->name === $name;
	}

	protected function getMessageType()
	{
		return $this->isValid() ? 'success' : 'error';
	}

	public function redirect()
	{
		$this->getParentControl()
			->redirect('this');
	}

	protected function attached($control)
	{
		if ( ! is_array($this->onSubmit)) {
			$this->onSubmit = [];
		}
		array_unshift($this->onSubmit, [
			$this,
			'message'
		]);
		$this->onSubmit[] = [
			$this,
			'redirect'
		];

		return parent::attached($control);
	}
}
