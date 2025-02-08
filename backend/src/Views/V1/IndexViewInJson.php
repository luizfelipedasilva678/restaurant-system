<?php

namespace App\Views\V1;

use App\Contracts\Index\IndexView;
use App\Presenters\IndexPresenter;
use App\Views\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexViewInJson extends View implements IndexView
{
  private IndexPresenter $presenter;

  public function __construct()
  {
    $this->presenter = new IndexPresenter($this);
  }

  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void
  {
    $this->response->getBody()->write(json_encode($data) ? json_encode($data) : '');
  }

  public function handleIndex(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;
    $this->presenter->getData();

    return $response;
  }
}
