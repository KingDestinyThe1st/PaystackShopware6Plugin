<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Core\Content\TransactionReference;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class TransactionReferenceEntity extends Entity
{
    use EntityIdTrait;
    

    protected ?string $reference;

    protected ?string $transaction_id;


    
    public function getReference(): ?string
    {
        return $this->reference;
    }

   public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getTransactionId(): ?string
    {
        return $this->transaction_id;
    }


    public function setTransactionId(?string $transaction_id): void
    {
        $this->transaction_id = $transaction_id;
    }
    
    
   
}