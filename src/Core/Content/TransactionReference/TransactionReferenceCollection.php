<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Core\Content\TransactionReference;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;


/**
 * @method void               add(ExampleEntity $entity)
 * @method void               set(string $key, ExampleEntity $entity)
 * @method TransactionReferenceEntity[]    getIterator()
 * @method TransactionReferenceEntity[]    getElements()
 * @method TransactionReferenceEntity|null get(string $key)
 * @method TransactionReferenceEntity|null first()
 * @method TransactionReferenceEntity|null last()
 */

class TransactionReferenceCollection extends EntityCollection
{
    
    protected function getExpectedClass(): string
    {
        return TransactionReferenceEntity::class;
    }


    public function getCollectionClass(): string
    {
        return TransactionReferenceCollection::class;
    }
    

}