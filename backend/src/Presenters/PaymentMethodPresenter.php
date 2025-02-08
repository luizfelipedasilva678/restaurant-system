<?php

namespace App\Presenters;

use App\Contracts\PaymentMethod\PaymentMethodView;
use App\Enums\HttpCodes;
use App\Exceptions\PaymentMethod\PaymentMethodPresenterException;
use App\Exceptions\PaymentMethod\PaymentMethodServiceException;
use App\Models\PaymentMethod\PaymentMethodMapper;
use App\Models\PaymentMethod\PaymentMethodRepositoryInRDB;
use App\Models\PaymentMethod\PaymentMethodService;
use App\Utils\PDOBuilder;

class PaymentMethodPresenter
{
  private PaymentMethodView $view;
  private PaymentMethodService $service;

  public function __construct(PaymentMethodView $view)
  {
    try {
      $this->view = $view;

      $repository = new PaymentMethodRepositoryInRDB(PDOBuilder::build());
      $this->service = new PaymentMethodService($repository);
    } catch (\PDOException $exception) {
      throw new PaymentMethodPresenterException('Error connecting to database', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function getPaymentsMethod(): void
  {
    try {
      $paymentsMethods = $this->service->getPaymentsMethods();

      $this->view->respondWith(PaymentMethodMapper::toDTOArray($paymentsMethods));
    } catch (PaymentMethodServiceException $exception) {
      throw new PaymentMethodPresenterException($exception->getMessage(), $exception->getCode());
    }
  }
}
