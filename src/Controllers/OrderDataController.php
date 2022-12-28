<?php

namespace src\Controllers;

use src\Api\Response;
use src\BO\OrderDataBO;
use src\Enums\FieldsEnum;
use src\Enums\HttpStatusCodeEnum;
use src\Factory\OrderDataDtoFactory;
use src\Tools\RequestTools;

class OrderDataController extends BasicController
{
    public OrderDataBO $bo;
    public OrderDataDtoFactory $factory;
    public array $fieldsToValidate;

    public function __construct()
    {
        $this->bo = new OrderDataBO();
        $this->factory = new OrderDataDtoFactory();
        $this->fieldsToValidate = FieldsEnum::getOrderDataInsertRequiredFields();
    }

    public function apiPost(\stdClass $object)
    {
        $this->bo->validatePostParamsApi($this->fieldsToValidate, $object);
        $itemToInsert = $this->factory->factoryToInsert($object);
        $this->bo->insert($itemToInsert);
        $this->bo->afterInsert($itemToInsert->getCartId());
        $inserted = $this->bo->findLastInserted();
        Response::render(HttpStatusCodeEnum::HTTP_CREATED, $this->factory->makePublic($inserted));
    }

    public function apiGet(int $id)
    {
        $item = $this->bo->findById($id);
        if ($item){
            Response::render(HttpStatusCodeEnum::HTTP_OK, $this->factory->makeCompletePublicOrder($item));
        }
        Response::renderNotFound();
    }

    public function apiDelete(int $id)
    {
        Response::renderMethodNotAllowed();
    }

    public function apiPut(\stdClass $object)
    {
        $id = (int)RequestTools::inputGet(FieldsEnum::ID);
        if (!$this->bo->validateStatusIsValid((int)$object->status)) {
            Response::renderInvalidFieldValue(FieldsEnum::STATUS);
        }
        $objectInDb = $this->bo->findById($id);
        $itemToUpdate = $this->factory->mergeObjectDbWitchObjectPut($objectInDb, $object);
        $this->bo->update($itemToUpdate);
        Response::render(HttpStatusCodeEnum::HTTP_OK, $this->factory->makeCompletePublicOrder($itemToUpdate));
    }
}