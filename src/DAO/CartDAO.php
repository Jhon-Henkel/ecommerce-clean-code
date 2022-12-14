<?php

namespace src\DAO;

use src\Database;
use src\DTO\CartDTO;
use src\Enums\OrderEnum;

class CartDAO extends BasicDAO
{
    public function getColumnsToInsert(): string
    {
        return 'cart_client_id, cart_hash, cart_gift_card_id, cart_order_done';
    }

    public function getParamsStringToInsert(): string
    {
        return ':client, :hash, :giftCard, :orderDone';
    }

    /**
     * @param CartDTO $item
     * @return array
     */
    public function getParamsArrayToInsert($item): array
    {
        return array(
            'client' => $item->getClientId(),
            'hash' => $item->getHash(),
            'giftCard' => $item->getGiftCardId(),
            'orderDone' => $item->getOrderDone()
        );
    }

    public function getUpdateSting(): string
    {
        $update = 'cart_client_id = :client, cart_hash = :hash,';
        $update .= ' cart_gift_card_id = :giftCard, cart_order_done = :orderDone';
        return $update;
    }

    public function getWhereClausuleToUpdate(): string
    {
        return 'cart_id = :id';
    }

    /**
     * @param CartDTO $item
     * @return array
     */
    public function getParamsArrayToUpdate($item): array
    {
        return array_merge($this->getParamsArrayToInsert($item), array('id' => $item->getId()));
    }

    public function IsClientWithCartOpenByClientId(int $clientId): bool
    {
        $db = new Database();
        $query = "SELECT * FROM cart WHERE cart_client_id = :clientId AND cart_order_done = :done";
        $count = $db->selectCount($query, array('clientId' => $clientId, 'done' => OrderEnum::ORDER_NOT_DONE));
        return (bool)$count;
    }
}