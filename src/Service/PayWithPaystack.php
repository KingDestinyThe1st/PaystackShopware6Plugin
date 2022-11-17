<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Service;

use Symfony\Component\HttpFoundation\Request;
use PaystackShopware6Plugin\Service\PaymentLink;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Shopware\Core\Checkout\Payment\Exception\CustomerCanceledAsyncPaymentException;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Shopware\Core\Framework\Uuid\Uuid;



class PayWithPaystack implements AsynchronousPaymentHandlerInterface
{
    private OrderTransactionStateHandler $transactionStateHandler;
    private PaymentLink $paymentLink;
    private $paymentState;
    //private $reference = 0;
    

    public function __construct(OrderTransactionStateHandler $transactionStateHandler, PaymentLink $paymentLink)
    {
        $this->transactionStateHandler = $transactionStateHandler;
        $this->paymentLink = $paymentLink;

        //$this->reference = $this->paymentLink->returnReference();

    }

    /**
     * @throws AsyncPaymentProcessException
     */
    public function pay(AsyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): RedirectResponse
    {
        // Method that sends the return URL to the external gateway and gets a redirect URL back
        try {
            
            $callback_url = $transaction->getReturnUrl();
            $email = $salesChannelContext->getCustomer()->getEmail();
            $amount = $transaction->getOrderTransaction()->getAmount()->getTotalPrice();
            $currency = 'NGN';//$transaction->getOrder()->getCurrency()->getIsoCode();
            $reference = Uuid::randomHex();
            $authorization = $this->paymentLink->returnSecretKey($salesChannelContext->getContext());
            
            $this->paymentLink->saveReferenceTransactionId(
                                                            $salesChannelContext->getContext(), 
                                                            $reference,
                                                            $transaction->getOrderTransaction()->getId()
                                                        );
            $redirectUrl = $this->paymentLink->returnPaymentLink(
                                                                $email, 
                                                                $amount, 
                                                                $currency, 
                                                                $reference, 
                                                                $authorization, 
                                                                $callback_url
                                                            );

        } catch (\Exception $e) {

            throw new AsyncPaymentProcessException(
                $transaction->getOrderTransaction()->getId(),
                'An error occurred during the communication with external payment gateway' . PHP_EOL . $e->getMessage()
            );

        }

        // Redirect to external gateway
        return new RedirectResponse($redirectUrl);
    }

    /**
     * @throws CustomerCanceledAsyncPaymentException
     */
    public function finalize(AsyncPaymentTransactionStruct $transaction, Request $request, SalesChannelContext $salesChannelContext): void
    {

        
        $transactionId = $transaction->getOrderTransaction()->getId();
        $authorization = $this->paymentLink->returnSecretKey($salesChannelContext->getContext());
        
        //dd($transactionId);
        //$allData = $this->paymentLink->wasPaymentSuccessful($salesChannelContext->getContext(), $transactionId);
       if($this->paymentLink->wasPaymentSuccessful($salesChannelContext->getContext(), $transactionId, $authorization) == 'success'){
            $paymentState = 'success';
       }
        //$allData = $this->paymentLink->checkPaymentSuccess($salesChannelContext->getContext(), $transactionId);

        //$this->paymentLink->verifyPayment('34ca80ff6c0d484e4690db8ebdc45ebf');

        // Example check if the user cancelled. Might differ for each payment provider
        if ($request->query->getBoolean('cancel')) {
            throw new CustomerCanceledAsyncPaymentException(
                $transactionId,
                'Customer canceled the payment on the Paystack payment page'
            );
        }

        // Example check for the actual status of the payment. Might differ for each payment provider
        //$paymentState = '';//$this->paymentLink->verifyPayment($transactionId);
        //$paymentState = 'completed';


        $context = $salesChannelContext->getContext();
        if ($paymentState === 'success') {
            // Payment completed, set transaction status to "paid"
            $this->transactionStateHandler->paid($transaction->getOrderTransaction()->getId(), $context);
            //dd('passed');
        } else {
            // Payment not completed, set transaction status to "open"
            $this->transactionStateHandler->reopen($transaction->getOrderTransaction()->getId(), $context);
        }
    }

    /**
    private function sendReturnUrlToExternalGateway(string $getReturnUrl): string
    {
        $paymentProviderUrl = '';
        // Do some API Call to your payment provider
        return $paymentProviderUrl;
    }
    */
}