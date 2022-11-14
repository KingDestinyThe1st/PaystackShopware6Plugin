<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Core\Content\Paystack;


use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;

class PaystackKeysDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'paystack_keys';
    
    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('name', 'name')),
            (new StringField('public_key', 'publicKey')),
            (new StringField('secret_key', 'secretKey')),
            (new StringField('payment_method_id', 'paymentMethodId'))
        ]);
    }

    public function getEntityClass(): string
    {
        return PaystackKeysEntity::class;
    }




}