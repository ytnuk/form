<?php

namespace WebEdit\Form;

use WebEdit;

abstract class Control extends WebEdit\Control {

    protected $facade;

    public function render() {
        $this->template->render($this->getTemplateFiles($this->entity ? 'edit' : 'add'));
    }

    public function handleAdd($form) {
        if (!$this->facade) {
            $this->presenter->error();
        }
        $entity = $this->facade->add($form->getValues());
        $this->presenter->redirect('Presenter:edit', ['id' => $entity->id]);
    }

    public function handleEdit($form) {
        if (!$this->facade || !$this->entity) {
            $this->presenter->error();
        }
        if ($form->submitted->name == 'delete') {
            $this->handleDelete();
        }
        $this->facade->edit($this->entity, $form->getValues());
        $this->presenter->redirect('this');
    }

    public function handleDelete() {
        if (!$this->facade || !$this->entity) {
            $this->presenter->error();
        }
        $this->facade->delete($this->entity);
        $this->presenter->redirect('Presenter:view');
    }

}
