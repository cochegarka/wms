<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ExcelAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Печать в Excel';
    }

    public function getIcon()
    {
        return 'voyager-file-text';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        $slug = $this->dataType->slug;
        $id = $this->data->{$this->data->getKeyName()};

        return "/excel/$slug/$id";
    }

    public function shouldActionDisplayOnDataType()
    {
        $slug = $this->dataType->slug;
        return $slug == 'incomes' || $slug == 'outcomes' || $slug == 'inventarizations';
    }
}