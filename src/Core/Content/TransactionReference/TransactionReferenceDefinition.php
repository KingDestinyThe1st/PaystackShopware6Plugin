<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Core\Content\TransactionReference;


use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;

class TransactionReferenceDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'paystack_transactions';
    
    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('reference', 'reference')),
            (new StringField('transaction_id', 'transactionId')),
        ]);
    }

    public function getEntityClass(): string
    {
        return TransactionReferenceEntity::class;
    }




}