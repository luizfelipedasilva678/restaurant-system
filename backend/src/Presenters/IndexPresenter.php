<?php

namespace App\Presenters;

use App\Contracts\Index\IndexView;
use App\Models\Index\IndexModel;

class IndexPresenter
{
  private IndexView $view;
  private IndexModel $model;

  public function __construct(IndexView $view)
  {
    $this->view = $view;
    $this->model = new IndexModel();
  }

  public function getData(): void
  {
    $this->view->respondWith($this->model->getData());
  }
}
