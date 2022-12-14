<?php

namespace src\BO;

use src\DAO\OrderDataDAO;
use src\DTO\CartDTO;
use src\Enums\FieldsEnum;
use src\Enums\OrderEnum;
use src\Enums\TableEnum;
use src\Exceptions\AttributesExceptions\AttributeNotFoundException;
use src\Exceptions\FieldsExceptions\InvalidFieldValueException;
use src\Factory\OrderDataDtoFactory;

class OrderDataBO extends BasicBO
{
    public OrderDataDAO $dao;
    public OrderDataDtoFactory $factory;

    public function __construct()
    {
        $this->dao = new OrderDataDAO(TableEnum::ORDER_DATA);
        $this->factory = new OrderDataDtoFactory();
    }

    public function validatePostParamsApi(array $paramsFields, \stdClass $item): void
    {
        $this->validateFieldsExist($paramsFields, $item);
        $clientBO = new ClientBO;
        if (!$clientBO->validateClientExistsById($item->clientId)) {
            throw new AttributeNotFoundException(FieldsEnum::CLIENT_ID_JSON);
        }
        $addressBO = new AddressBO();
        if (!$addressBO->validateAddressExistsById($item->addressId)) {
            throw new AttributeNotFoundException(FieldsEnum::ADDRESS_ID_JSON);
        }
        $cartBO = new CartBO();
        $cartBO->validateCartToOrderById($item->cartId);
        if (!$this->validateStatusAptToInsert($item->status)) {
            throw new InvalidFieldValueException(FieldsEnum::STATUS);
        }
    }

    public function validateStatusAptToInsert(int $status): bool
    {
        if ($status == OrderEnum::STATUS_PENDENTE || $status == OrderEnum::STATUS_PAGO) {
            return true;
        }
        return false;
    }

    public function validateStatusIsValid(int $status): bool
    {
        $statusList = OrderEnum::getListOfAllStatus();
        foreach ($statusList as $item => $key) {
            if ($status == $key) {
                return true;
            }
        }
        return false;
    }

    public function calculateTotalOrderValue(
        float|int$itensValue,
        float|int$extraFare,
        float|int$giftCardValue,
        float|int$shippingValue
    ): float|int
    {
        return ($itensValue + $extraFare + $shippingValue) - $giftCardValue;
    }

    public function afterInsert(int $cartId): void
    {
        $cartBO = new CartBO();
        /** @var CartDTO $cart */
        $cart = $cartBO->findById($cartId);
        $cartBO->updateCartOrderDone($cart);
        $cartItemBO = new CartItemBO();
        $cartItemBO->updateStockItensPurchaseByCartId($cartId);
        if ($cart->getGiftCardId()) {
            $giftCardBO = new GiftCardBO();
            $giftCardBO->updateGiftCardUsageById($cart->getGiftCardId());
        }
    }
}