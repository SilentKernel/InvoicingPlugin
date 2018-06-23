<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceIdentifierGenerator;

final class InvoiceGeneratorSpec extends ObjectBehavior
{
    function let(InvoiceIdentifierGenerator $invoiceIdentifierGenerator): void
    {
        $this->beConstructedWith($invoiceIdentifierGenerator);
    }

    function it_is_an_invoice_generator(): void
    {
        $this->shouldImplement(InvoiceGeneratorInterface::class);
    }

    function it_generates_an_invoice_for_a_given_order(
        InvoiceIdentifierGenerator $invoiceIdentifierGenerator,
        OrderInterface $order,
        AddressInterface $billingAddress,
        AdjustmentInterface $shippingAdjustment,
        ProductVariantInterface $variant,
        OrderItemInterface $orderItem
    ): void {
        $date = new \DateTimeImmutable('now');

        $invoiceIdentifierGenerator->__invoke('007')->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $order->getNumber()->willReturn('007');
        $order->getCurrencyCode()->willReturn('USD');
        $order->getTaxTotal()->willReturn(300);
        $order->getTotal()->willReturn(10300);
        $order->getBillingAddress()->willReturn($billingAddress);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn(new ArrayCollection([$shippingAdjustment->getWrappedObject()]));

        $billingAddress->getFirstName()->willReturn('John');
        $billingAddress->getLastName()->willReturn('Doe');
        $billingAddress->getCountryCode()->willReturn('US');
        $billingAddress->getStreet()->willReturn('Foo Street');
        $billingAddress->getCity()->willReturn('New York');
        $billingAddress->getPostcode()->willReturn('21354');
        $billingAddress->getProvinceCode()->willReturn(null);
        $billingAddress->getProvinceName()->willReturn(null);
        $billingAddress->getCompany()->willReturn(null);

        $orderItem->getProductName()->willReturn('Mjolnir');
        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnitPrice()->willReturn(5000);
        $orderItem->getSubtotal()->willReturn(10000);
        $orderItem->getTaxTotal()->willReturn(300);
        $orderItem->getTotal()->willReturn(10300);
        $orderItem->getVariantName()->willReturn('Blue');
        $orderItem->getVariant()->willReturn($variant);

        $variant->getCode()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $shippingAdjustment->getLabel()->willReturn('UPS');
        $shippingAdjustment->getAmount()->willReturn(800);

        $this->generateForOrder($order, $date)->shouldReturnAnInstanceOf(Invoice::class);
    }
}