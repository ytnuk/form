<?php

namespace WebEdit\Form;

use WebEdit\Database;
use WebEdit\Form;

interface Factory
{

	/**
	 * @return Form
	 */
	public function create();
}
