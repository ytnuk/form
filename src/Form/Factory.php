<?php

namespace WebEdit\Form;

use WebEdit;

/**
 * Interface Factory
 *
 * @package WebEdit\Form
 */
interface Factory
{

	/**
	 * @return WebEdit\Form
	 */
	public function create();
}
