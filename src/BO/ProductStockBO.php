<?php

namespace src\BO;

use src\DAO\ProductDAO;
use src\DAO\ProductStockDAO;
use src\DTO\ProductStockDTO;
use src\Enums\FieldsEnum;
use src\Enums\TableEnum;
use src\Exceptions\AttributesExceptions\AttributeNotFoundException;
use src\Exceptions\ProductExceptions\ProductIsLinkedOnCartException;
use src\Factory\ProductStockDtoFactory;

class ProductStockBO extends BasicBO
{
    public ProductStockDAO $dao;
    public ProductStockDtoFactory $factory;

    public function __construct()
    {
        $this->dao = new ProductStockDAO(TableEnum::PRODUCT_STOCK);
        $this->factory = new ProductStockDtoFactory();
    }

    public function validatePostParamsInProductInsertApi(array $paramsFields, \stdClass $stock): void
    {
        $this->validateFieldsExist($paramsFields, $stock);
        $this->validateItemValueMustNotExistsInDb(FieldsEnum::getBasicRequiredFields(), $stock);
        $this->validationsAttributesIdsExistsForApi($stock);
    }

    public function validatePostParamsApi(array $paramsFields, \stdClass $item): void
    {
        $this->validateFieldsExist($paramsFields, $item);
        $this->validateProductExistsForApiById($item->productId);
        $this->validationsAttributesIdsExistsForApi($item);
        parent::validatePostParamsApi(FieldsEnum::getBasicRequiredFields(), $item);
    }

    public function validatePutParamsApi(array $paramsFields, \stdClass $item): void
    {
        $this->validateFieldsExist($paramsFields, $item);
        $this->validateProductExistsForApiById($item->productId);
        $this->validationsAttributesIdsExistsForApi($item);
        parent::validatePutParamsApi(FieldsEnum::getBasicRequiredFields(), $item);
    }

    public function insertMultipleStocks(array $stocks): void
    {
        foreach ($stocks as $stock) {
            $this->insert($stock);
        }
    }

    public function findByProductId(int $id): null|array
    {
        $stocks = $this->dao->findByProductId($id);
        $stocksFind = array();
        foreach ($stocks as $stock) {
            $stocksFind[] = $this->factory->populateDbToDto($stock);
        }
        return $stocksFind;
    }

    public function deleteAllStocksByProductId(int $id): void
    {
        $stocks = $this->findByProductId($id);
        if (!$stocks) {
            return;
        }
        foreach ($stocks as $stock) {
            $cartItemBO = new CartItemBO();
            if (!$cartItemBO->validateStockIsNotCartDone($stock->getId())) {
                throw new ProductIsLinkedOnCartException();
            }
            $cartItemBO->deleteByStockId($stock->getId());
        }
        $this->dao->deleteAllByProductId($id);
    }

    public function validationsAttributesIdsExistsForApi(\stdClass $item): void
    {
        $this->validateColorExistsForApiById($item->colorId);
        $this->validateSizeExistsForApiById($item->sizeId);
        $this->validateBrandExistsForApiById($item->brandId);
    }

    public function validateProductExistsForApiById(int $id): void
    {
        $productBO = new ProductBO();
        if (!$productBO->countById($id)) {
            throw new AttributeNotFoundException(FieldsEnum::PRODUCT_ID_JSON);
        }
    }

    public function validateColorExistsForApiById(int $id): void
    {
        $colorBO = new ColorBO();
        if (!$colorBO->countById($id)) {
            throw new AttributeNotFoundException(FieldsEnum::COLOR_ID_JSON);
        }
    }

    public function validateSizeExistsForApiById(int $id): void
    {
        $sizeBO = new SizeBO();
        if (!$sizeBO->countById($id)) {
            throw new AttributeNotFoundException(FieldsEnum::SIZE_ID_JSON);
        }
    }

    public function validateBrandExistsForApiById(int $id): void
    {
        $brandBO = new BrandBO();
        if (!$brandBO->countById($id)) {
            throw new AttributeNotFoundException(FieldsEnum::BRAND_ID_JSON);
        }
    }

    public function decreaseStockBalanceByStockId(int $id, int $decrease): void
    {
        /** @var ProductStockDTO $stock */
        $stock = $this->findById($id);
        $stock->setQuantity(($stock->getQuantity() - $decrease));
        $this->update($stock);
    }
}