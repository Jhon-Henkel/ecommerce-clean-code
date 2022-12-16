<?php

namespace src\Controllers;

use src\Api\Response;
use src\BO\AddressBO;
use src\BO\ClientBO;
use src\Enums\FieldsEnum;
use src\Enums\HttpStatusCodeEnum;
use src\Factory\ClientDtoFactory;

class ClientController extends BasicController
{
    public ClientBO $bo;
    public ClientDtoFactory $factory;
    public array $fieldsToValidate;

    public function __construct()
    {
        $this->bo = new ClientBO();
        $this->factory = new ClientDtoFactory();
        $this->fieldsToValidate = FieldsEnum::getClientRequiredFields();
    }

    public function apiPost(\stdClass $object)
    {
        $addressBO = new AddressBO();
        $this->bo->validatePostParamsApi($this->fieldsToValidate, $object);
        $this->bo->validateAddressInClientInsert($object->address);
        $clientToInsert = $this->factory->factory($object);
        $this->bo->insert($clientToInsert);
        $clientInserted = $this->bo->findLastInserted();
        $addressToInsert = $this->bo->factoryAddressToInsertInClient($object->address, $clientInserted->getId());
        $addressBO->insert($addressToInsert);
        $addressInserted = $addressBO->findLastInserted();
        $clientWithAddress = $this->factory->factoryClientWithAddress($clientInserted, $addressInserted);
        Response::render(HttpStatusCodeEnum::HTTP_CREATED, $clientWithAddress);
    }
}