<?php 

namespace App\Factory;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Shop;

/**
 * Class OrderFactory
 * @package App\Factory
 */
class OrderFactory
{
    /**
     * Creates an order.
     *
     * @return Order
     */
    public function create(): Order
    {
        $order = new Order();
        $order
            ->setStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());
        
        return $order;
    }

    /**
     * Creates an item for a product.
     *
     * @param Product $product
     *
     * @return OrderItem
     */
    public function createItem(Shop $shop): OrderItem
    {
        $item = new OrderItem();
        $item->setProduct($shop);
        $item->setQuantity(1);

        return $item;
    }

}


?>