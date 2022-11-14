<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Core\Content\Paystack;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;


/**
 * @method void               add(ExampleEntity $entity)
 * @method void               set(string $key, ExampleEntity $entity)
 * @method PaystackKeysEntity[]    getIterator()
 * @method PaystackKeysEntity[]    getElements()
 * @method PaystackKeysEntity|null get(string $key)
 * @method PaystackKeysEntity|null first()
 * @method PaystackKeysEntity|null last()
 */

class ExampleCollection extends EntityCollection
{
    
    protected function getExpectedClass(): string
    {
        return PaystackKeysEntity::class;
    }


    public function getCollectionClass(): string
    {
        return PaystackKeysCollection::class;
    }
    

}