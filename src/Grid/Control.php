<?php

namespace WebEdit\Grid;

use Nette\Forms;
use WebEdit\Application;

final class Control extends Application\Control
{

	/**
	 * @persistent
	 */
	public $order = [];
	/**
	 * @persistent
	 */
	public $filter = [];
	private $form;
	private $items;
	private $active;
	private $link;
	private $filteredInputs = [];
	private $limitInputs;

	public function __construct(callable $form, $items)
	{
		$this->form = $form;
		$this->items = $items;
	}

	public function filterInputs(array $filteredInputs)
	{
		$this->filteredInputs = $filteredInputs;

		return $this;
	}

	public function filter($button)
	{
		if ($button->getHtmlName() !== 'filter') {
			return;
		}
		$this->filter = $this->prepareFilterValues($button->getForm()->getValues(TRUE));
		$this->redirect('this');
	}

	private function prepareFilterValues(array $values)
	{
		$data = [];
		foreach ($values as $key => $value) {
			if (is_array($value)) {
				$value = $this->prepareFilterValues($value);
			}
			if ($value) {
				$data[$key] = $value;
			}
		}

		return $data;
	}

	public function handleOrder($htmlName)
	{
		$this->order = $this->prepareOrderValues($this->htmlNameToArray($htmlName), $this->order);
		$this->redirect('this');
	}

	private function prepareOrderValues(array $keys, array $values)
	{
		$key = array_shift($keys);
		end($values);
		$active = $key === key($values);
		$value = isset($values[$key]) ? $values[$key] : [];
		unset($values[$key]);
		$values[$key] = count($keys) ? $this->prepareOrderValues($keys, $value) : (! $active ? 'ASC' : ($value === 'ASC' ? 'DESC' : NULL));

		return $values;
	}

	private function htmlNameToArray($htmlName)
	{
		return explode('[', str_replace([']'], NULL, $htmlName));
	}

	public function setLink($link)
	{
		$this->link = $link;

		return $this;
	}

	public function limitInputs($limit)
	{
		$this->limitInputs = $limit;

		return $this;
	}

	protected function attached($control)
	{
		parent::attached($control);
		$this->active = $this->getParameter('active');
		$this->items = call_user_func($this->items, $this->order, $this->filter);
		if ( ! is_array($this->items)) {
			$this->items = iterator_to_array($this->items);
		}
		if ( ! $header = array_search(NULL, $this->items)) {
			$this->items = array_reverse($this->items, TRUE);
			$this->items[] = NULL;
			$this->items = array_reverse($this->items, TRUE);
			$keys = array_keys($this->items);
			$header = reset($keys);
		}
		$this['form'][$header]->setDefaults($this->filter)->addSubmit('filter', 'grid.filter')->setValidationScope(FALSE)->onClick[] = [$this, 'filter'];
		foreach ($this->items as $key => $item) {
			$controls = [];
			$form = $this['form'][$key];
			foreach ($form->getControls() as $control) {
				$controls[$control->getHtmlName()] = $control;
			}
			$inputsCount = 0;
			$this->items[$key] = (object) ['id' => $key, 'item' => $item, 'form' => $form, 'inputs' => array_filter($controls, function ($control) use ($form, $item, &$inputsCount) {
				if ($this->limitInputs && $inputsCount > $this->limitInputs) {
					return FALSE;
				}
				$inputsCount++;
				if ($item === NULL) {
					$control->setAttribute('onchange', 'this.form.filter.click()');
				}
				$filtered = ! (bool) $this->filteredInputs;
				foreach ($this->filteredInputs as $name) {
					if (strpos($control->getHtmlName(), $name) === 0) {
						$filtered = TRUE;
						break;
					}
				}

				return $filtered && ! $control instanceof Forms\Controls\HiddenField;
			}), 'hidden' => array_filter($controls, function ($control) {
				return $control instanceof Forms\Controls\HiddenField;
			}), 'link' => is_callable($this->link) ? call_user_func($this->link, $item) : $this->link, 'active' => $key !== $header ? $this->active === (string) $key : TRUE,];
		}
	}

	protected function startup()
	{
		$this->template->items = $this->items;
		$this->template->filter = $this->filter;
		$this->template->orderBy = $this->arrayToHtmlName($this->order, $sort);
		$this->template->order = $sort;
		$this->template->filteredInputs = $this->filteredInputs;
	}

	private function arrayToHtmlName(array $values, &$value = NULL, $wrap = FALSE)
	{
		$value = end($values);
		$key = key($values);
		if ($wrap) {
			$key = '[' . $key . ']';
		}

		return $key . (is_array($value) ? $this->arrayToHtmlName($value, $value, TRUE) : NULL);
	}

	protected function createComponentForm()
	{
		return new Application\Control\Multiplier(function ($key) {
			return call_user_func($this->form, $this->items[$key]);
		});
	}
}