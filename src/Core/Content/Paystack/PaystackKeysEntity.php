<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Core\Content\Paystack;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PaystackKeysEntity extends Entity
{
    use EntityIdTrait;
    

    protected ?string $name;

    protected ?string $public_key;

    protected ?string $secret_key;

    protected ?string $payment_method_id;

    
    public function getName(): ?string
    {
        return $this->name;
    }

   public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPublicKey(): ?string
    {
        return $this->public_key;
    }

    public function getPaymentMethodId(): ?string
    {
        return $this->payment_method_id;
    }

    public function setPublicKey(?string $public_key): void
    {
        $this->public_key = $public_key;
    }
    
    public function getSecretKey(): ?string
    {
        return $this->secret_key;
    }

    public function setSecretKey(?string $secret_key): void
    {
        $this->secret_key = $secret_key;
    }

    public function setPaymentMethodId(?string $payment_method_id): void
    {
        $this->payment_method_id = $payment_method_id;
    }
   
}