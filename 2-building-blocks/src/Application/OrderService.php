<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Order;
use App\Domain\OrderRepository;
use App\Domain\OrderWasCancelled;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\EventBus;

final class OrderService
{
    #[CommandHandler]
    public function placeOrder(
        PlaceOrder $placeOrder, OrderRepository $orderRepository
    ): void
    {
        $order = Order::create($placeOrder->orderId, $placeOrder->productName);
        $orderRepository->save($order);
    }

    #[CommandHandler]
    public function cancelOrder(
        CancelOrder $command, OrderRepository $orderRepository,
        EventBus $eventBus
    ) {
        $order = $orderRepository->get($command->orderId);
        $order->cancel();
        $orderRepository->save($order);

        $eventBus->publish(new OrderWasCancelled($command->orderId));
    }
}