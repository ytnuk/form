<?php

namespace WebEdit;

use Nette\Application;

class Form extends Application\UI\Form
{

    public $onAdd = [];
    public $onEdit = [];
    public $onDelete = [];
    private $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
        $this->monitor(Form\Control::class);
    }

    protected function attached($control)
    {
        parent::attached($control);
        if (!$control instanceof Form\Control) {
            return;
        }
        if ($this->entity) {
            $this->addSubmit('edit', 'form.button.save');
            $this->addSubmit('delete', 'form.button.delete')->setValidationScope(FALSE);
        } else {
            $this->addSubmit('add', 'form.button.add');
        }
        $this->onSubmit[] = function ($form) use ($control) {
            if (!$this->entity) {
                $entity = $this->onAdd($form->getValues());
                dump($entity);
                exit;
                $this->presenter->redirect('Presenter:edit', ['id' => $entity->id]);
            } elseif ($form->submitted->name === 'delete') {
                $this->onDelete($this->entity, $form->getValues());
                $this->presenter->redirect('Presenter:view');
            } else {
                $this->onEdit($this->entity, $form->getValues());
                $this->presenter->redirect('this');
            }
        };
    }

}
