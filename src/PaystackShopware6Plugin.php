<?php declare(strict_types=1);

namespace PaystackShopware6Plugin;


use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class PaystackShopware6Plugin extends Plugin
{
    /**
     * //get and return paystack plugin Id
     * @param Context $context
     * @return string $pluginId
     */
    public function getPluginId(Context $context): string
    {
        $pluginIdProvider = $this->container->get(PluginIdProvider::class);
        $pluginId = $pluginIdProvider->getPluginIdByBaseClass(PaystackShopware6Plugin::class, $context);
        return $pluginId;
    }

    /**
     * //payment method repository
     * @return paymentMethodRepository
     */

    public function getPaymentRepository(): object
    {
        $paymentRepository = $this->container->get('payment_method.repository');
        return $paymentRepository;
    }


    /**
     * @param Context
     * @return paystackPaymentMethodData
     */
    public function getPaymentMethod(Context $context): ?object
    {
        $criteria = new Criteria();
        $criteria = $criteria->addFilter(new EqualsFilter('pluginId', $this->getPluginId($context)));
        $paystackPaymentMethodData = $this->getPaymentRepository()->search($criteria, $context)->first();
        return $paystackPaymentMethodData;
    }

    
    /**
     * Register Paystack as a payment method
     * @param Context
     * @return void
     */
    public function addPaymentMethod(Context $context): void
    {   
        if(!is_null($this->getPaymentMethod($context))){
            return;
        }


        $paymentData = [
        
            'handlerIdentifier' => "PaystackShopware6Plugin\Service\PayWithPaystack",
            'pluginId' => $this->getPluginId($context),
            'afterOrderEnabled' => true,
            'translations' => [
                'de-DE' => [
                    'description' => 'Bezahlen Sie Mit Paystack. Sicher, Sicher Und Einfach Zu Bedienen',
                    'name' => 'Paystack',
                ],
                'en-GB' => [
                    'description' => 'Pay With Paystack. Safe, Secure, and Easy To Use.',
                    'name' => 'Paystack',
                ],
                'fr-FR' => [
                    'description' => 'Payez Avec Paystack. Sûr, Sécurisé Et Facile À Utiliser',
                    'name' => 'Paystack',
                ]
            ],

        ];
        

        $this->getPaymentRepository()->create([$paymentData], $context);

        return;

    }


    //paystack_keys table starts

    /**
     * @return object paystackKeysRepository
     */
    public function getPaystackKeysRepository(): object
    {
        $paystackKeysRepository = $this->container->get('paystack_keys.repository');
        return $paystackKeysRepository;
    }

    /**
     * //return paystack_keys table data
     * @param Context $context
     * @return object $paystackKeys
     */
    public function getPaystackKeysData(Context $context): ?object
    {
        $criteria = new Criteria();
        $criteria = $criteria->addFilter(new EqualsFilter('name', 'paystack_paystack'));
        $paystackKeys = $this->getPaystackKeysRepository()->search($criteria, $context)->first();
        return $paystackKeys;
    }

    
    /**
     * //add data to the paystack_keys table if table empty
     * @param Context $context
     */
    public function addPaystackDataKeys(Context $context): void
    {
        if(!is_null($this->getPaystackKeysData($context))){
            return;
        }

        $paystackKeysData = [
            'name' => 'paystack_paystack',
            'publicKey' => '',
            'secretKey' => 'sk_test_4822077a8eb36c205e3807b3aacd9711af768731',
            'paymentMethodId' => $this->getPaymentMethod($context)->getId()
        ];

        $this->getPaystackKeysRepository()->create([$paystackKeysData], $context);

        return;
    }

    //paystack_keys table ends


    /**
     * //
     * @param Context $context
     * @param Bool $bool 
     */
    public function activateDeactivate(Context $context, bool $bool): void
    {
        if(is_null($this->getPaymentMethod($context))){
            return;
        }

        $this->getPaymentRepository()->update([
            [   'id' => $this->getPaymentMethod($context)->getId(),
                'active' => $bool
            ]
        ], $context);

        return;
    }


    public function install(InstallContext $installContext): void
    {
        $this->addPaymentMethod($installContext->getContext());
    }


    public function uninstall(UninstallContext $uninstallContext): void
    {
        $this->activateDeactivate($uninstallContext->getContext(), false);
    }

    public function activate(ActivateContext $activateContext): void
    {
        $this->activateDeactivate($activateContext->getContext(), true); 
        $this->addPaystackDataKeys($activateContext->getContext());
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->activateDeactivate($deactivateContext->getContext(), false); 
    }
    

        

}