<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Subscriber;

use Shopware\Core\Content\Product\ProductEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class MySubscriber implements EventSubscriberInterface
{
    private EntityRepositoryInterface $paystackTransactionsRepository;
    private EntityRepositoryInterface $paymentMethodRepository;

   public function __construct(
                                EntityRepositoryInterface $paystackTransactionsRepository, 
                                EntityRepositoryInterface $paymentMethodRepository 
                            )
    {
        $this->paystackTransactionsRepository = $paystackTransactionsRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public static function getSubscribedEvents(): array
    {
        // Return the events to listen to as array like this:  <event to listen to> => <method to execute>
        return [
            ProductEvents::PRODUCT_LOADED_EVENT => 'onProductsLoaded'
        ];
    }

    public function onProductsLoaded(EntityLoadedEvent $event)
    {

        $context = $event->getContext();
    
    /**
        $pluginIdProvider = $this->container->get(PluginIdProvider::class);
        $pluginId = $pluginIdProvider->getPluginIdByBaseClass(PaystackShopware6Plugin::class, $event->getContext());

        $criteria = new Criteria();
        $criteria = $criteria->addFilter(new EqualsFilter('pluginId', $pluginId($event->getContext())));
        $paymentRepository = $this->container->get('payment_method.repository');
        $paymentMethod = $paymentRepository->search($criteria, $event->getContext())->first();
        dd($paymentMethod);
    */

    $criteria = new Criteria();
    $criteria->addFilter(new EqualsFilter('name', 'paystack'));
    $paymentMethodData = $this->paymentMethodRepository->search($criteria, $context)->first();
    //dd($paymentMethodData->getId());

    //$criteria = new Criteria();
    //$criteria = $criteria->addFilter(new EqualsFilter('pluginId', $pluginId($event->getContext())));

    

    }
}