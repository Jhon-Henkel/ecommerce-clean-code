<?php

namespace src\Factory;

use Exception;
use src\DTO\ProductStockDTO;
use src\Tools\DateTools;
use stdClass;

class ProductStockDtoFactory extends BasicDtoFactory
{
    public function factory(stdClass $item): ProductStockDTO
    {
        if (!isset($item->createdAt)) {
            $item->createdAt = null;
        }
        if (!isset($item->updatedAt)) {
            $item->updatedAt = null;
        }
        $productStockFactored = new ProductStockDTO();
        $productStockFactored->setId($item->id ?? null);
        $productStockFactored->setCode($item->code);
        $productStockFactored->setName($item->name);
        $productStockFactored->setQuantity($item->quantity);
        $productStockFactored->setColorId($item->colorId);
        $productStockFactored->setSizeId($item->sizeId);
        $productStockFactored->setBandId($item->brandId);
        $productStockFactored->setProductId($item->productId);
        $productStockFactored->setPrice($item->price);
        $productStockFactored->setWidth($item->width);
        $productStockFactored->setHeight($item->height);
        $productStockFactored->setLength($item->length);
        $productStockFactored->setGrossWeight($item->grossWeight);
        $productStockFactored->setCreatedAt($item->createdAt ?? null);
        $productStockFactored->setUpdatedAt($item->updatedAt ?? null);
        return $productStockFactored;
    }

    /**
     * @param ProductStockDTO $item
     * @return stdClass
     *
     */
    public function makePublic($item): stdClass
    {
        $stockPublic = new stdClass();
        $stockPublic->id = $item->getId();
        $stockPublic->code = $item->getCode();
        $stockPublic->name = $item->getName();
        $stockPublic->quantity = $item->getQuantity();
        $stockPublic->colorId = $item->getColorId();
        $stockPublic->sizeId = $item->getSizeId();
        $stockPublic->brandId = $item->getBandId();
        $stockPublic->productId = $item->getProductId();
        $stockPublic->price = $item->getPrice();
        $stockPublic->width = $item->getWidth();
        $stockPublic->height = $item->getHeight();
        $stockPublic->length = $item->getLength();
        $stockPublic->grossWeight = $item->getGrossWeight();
        $stockPublic->createdAt = DateTools::dateTimeToStringConverter($item->getCreatedAt());
        $stockPublic->updatedAt = DateTools::dateTimeToStringConverter($item->getUpdatedAt());
        return $stockPublic;
    }

    /**
     * @throws Exception
     */
    public function populateDbToDto(array $item): ProductStockDTO
    {
        $productStockDTO = new ProductStockDTO();
        $productStockDTO->setId($item['product_stock_id']);
        $productStockDTO->setCode($item['product_stock_code']);
        $productStockDTO->setName($item['product_stock_name']);
        $productStockDTO->setQuantity($item['product_stock_quantity']);
        $productStockDTO->setColorId($item['product_stock_color_id']);
        $productStockDTO->setSizeId($item['product_stock_size_id']);
        $productStockDTO->setBandId($item['product_stock_brand_id']);
        $productStockDTO->setProductId($item['product_stock_product_id']);
        $productStockDTO->setPrice($item['product_stock_price']);
        $productStockDTO->setWidth($item['product_stock_width']);
        $productStockDTO->setHeight($item['product_stock_height']);
        $productStockDTO->setLength($item['product_stock_length']);
        $productStockDTO->setGrossWeight($item['product_stock_gross_weight']);
        $productStockDTO->setCreatedAt(DateTools::stringToDateTimeConverter($item['product_stock_created_at']));
        $productStockDTO->setUpdatedAt(DateTools::stringToDateTimeConverter($item['product_stock_updated_at']));
        return $productStockDTO;
    }
}