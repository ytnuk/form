<?php

namespace Ytnuk\Form;

use Ytnuk;

/**
 * Class Control
 *
 * @package Ytnuk\Form
 */
abstract class Control extends Ytnuk\Application\Control
{

	/**
	 * @return Ytnuk\Form
	 */
	abstract protected function createComponentYtnukForm();
}
