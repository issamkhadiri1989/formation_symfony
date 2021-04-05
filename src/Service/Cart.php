<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Cart as CartEntity;
use App\Entity\CartLine;
use App\Entity\Product as Item;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * This service aims to  manage the cart.
 */
class Cart
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Adds a product to the cart.
     *
     * @param Item $product
     *
     * @throws \Exception
     */
    public function addToCart(Item $product): void
    {
        $cart = null;
        if ($this->isCartEmpty() === true) {
            $cart = $this->createNewCart();
            $this->session->set('cart', $cart);
        } else {
            /** @var CartEntity $cart */
            $cart = $this->session->get('cart');
        }
        $this->addItemToCart($cart, $product);
        $this->updateCart($cart);
    }

    /**
     * Adds a new product to cart.
     *
     * @param CartEntity $cart
     * @param Item       $product
     */
    public function addItemToCart(CartEntity  $cart, Item $product): void
    {
        $cart->addCartLine((new CartLine())
            ->setProduct($product)
            ->setQuantity(1));
    }

    /**
     * Updates the current cart state.
     *
     * @param CartEntity $cart
     */
    public function updateCart(CartEntity $cart): void
    {
        $this->session->set('cart', $cart);
    }

    /**
     * Clears the cart.
     */
    public function clearCart(): void
    {
        if ($this->isCartEmpty() === false) {
            $this->session->set('cart', null);
        }
    }

    /**
     * Checks if the cart is empty or not already set.
     *
     * @return bool
     */
    public function isCartEmpty(): bool
    {
        return (!$this->session->has('cart') || $this->session->get('cart') === null);
    }

    /**
     * Creates new cart.
     *
     * @return CartEntity
     *
     * @throws \Exception
     */
    private function createNewCart(): CartEntity
    {
        return (new CartEntity())
            ->setCreatedAt(new \DateTime())
            ->setStatus(CartEntity::CART_CREATED);
    }
}