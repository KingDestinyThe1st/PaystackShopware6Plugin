
const { Component, Mixin} = Shopware;
const { Criteria } = Shopware.Data;


import template from './paystack-settings-page.html.twig';

Component.register('paystack-settings-page', {

   template,

   inject: [
    'repositoryFactory',
    'syncService'
    
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

   metaInfo() {
      return {
          title: this.$createTitle()
      };
    },

 // Component: [paystack],

  data: function () {

    return {

        id: null,
        entity: undefined,
        secretKey: null,
        publicKey: null,
        paymentMethodId: null,
        isDefaultForStorefront: undefined,
	    isDefaultForHeadless: undefined,
	    isActivatedforStorefront: undefined,
	    isActivatedForHeadless: undefined,
        salesChannels: null,
        salesChannelsAvailability: {},
        salesChannelsDefault: {},
        falseBoolean: false,
        defaultPaymentMethod: [],

        salesChannelsData: [],
        salesChannelData: {},
        isLoading: true

    }

   },

   computed: {

        paystackKeysRepository() {
            return this.repositoryFactory.create('paystack_keys');
        },

        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },

        salesChannelPaymentMethodRepository() {
            return this.repositoryFactory.create('sales_channel_payment_method');
        },

    
   },


   methods: {


    makeDefault(salesChannelId){

        const criteria = new Criteria();

        this.salesChannelRepository
                .search(criteria, Shopware.Context.api)
                .then(result => {

                    this.salesChannels = result;

                    //loop through saleschannel to identify the target saleschannelId
                    for(let i = 0; i < result.length; i++){

                        //if target saleschannel has been identified
                        if(result[i].id === salesChannelId){
  
                            //loop through the choosen saleschannel payment methods to find paystack payment method
                            for(let v = 0; v < result[i].paymentMethodIds.length; v++){

                                //if paystack payment method is found in the choosen saleschannel payment methods
                                if( result[i].paymentMethodIds[v] === this.paymentMethodId ){
                                    
                                    //then make it the default payment method for the selected saleschannel
                                    this.makeDefaultPaymentMethod(salesChannelId);

                                }else{
                                    //console.log('no');
                                }
                            }

                        }else{

                            //console.log('no');

                        }


                    }


                    //console.log(result);
                });

    },

        /**
         * @description Retrieves relevant data from database tables and populates the data fields
         * 1. paystack_keys
         * 2. sales_channel
        **/
        getPaystackData(){

            this.isLoading = true;
        
            const criteria = new Criteria();

            this.paystackKeysRepository
                .search(criteria, Shopware.Context.api)
                .then(result => {
                    this.secretKey = result[0].secretKey;
                    this.publicKey = result[0].publicKey;
                    this.paymentMethodId = result[0].paymentMethodId;
                    this.id = result[0].id;
                    //console.log(result);
                });

                this.salesChannelRepository
                .search(criteria, Shopware.Context.api)
                .then(result => {
                    this.salesChannels = result;
                    //console.log(this.paymentMethodId);
                });


               // this.isLoading = false;
                
                
      
        },


        /**
         * @description update the public and secret keys field
         * in paystack_keys table
         * @returns void
         */

        updateSecretPublicKeys(){

            this.isLoading = true;

            this.paystackKeysRepository
                .get(this.id, Shopware.Context.api)
                .then((update) => {
                    update.publicKey = this.publicKey;
                    update.secretKey = this.secretKey;
                    this.paystackKeysRepository.save(update, Shopware.Context.api);
                });

           // this.getPaystackData();
           this.isLoading = false;
        },

        /**
         * @description Update the payment_method_id field of the
         * sales_channel table with Paystack payment method Id
         * @param {*} salesChannelId 
         * @returns void
         */
        makeDefaultPaymentMethod(salesChannelId){

            this.isLoading = true;


                this.salesChannelRepository
                        .get(salesChannelId, Shopware.Context.api)
                        .then((update) => {
                            update.paymentMethodId = this.paymentMethodId;
                            this.salesChannelRepository.save(update, Shopware.Context.api);
                         });
                    
           

            this.isLoading = false;

           alert('Paystack Has Been Set As Default Payment Method');
           //this.createNotificationSuccess({ title: 'Paystack Has Been Set As Default Payment Method' })
        
    
        },


        /**
         * @description insert Paystack payment method Id into sales_channel_payment_method table
         * if it does not exist in sales_channel table payment_method_ids column
         * @param {*} salesChannelId 
         */    
        activatePaymentMethod(salesChannelId){

            this.isLoading = true;

            //if(this.ifPaystackPaymentMethodExist(salesChannelId) !== 'paystackExist'){

                this.entity = this.salesChannelPaymentMethodRepository.create(Shopware.Context.api);
                this.entity.paymentMethodId = this.paymentMethodId;
                this.entity.salesChannelId = salesChannelId;
                this.salesChannelPaymentMethodRepository.save(this.entity, Shopware.Context.api);
                //console.log(this.entity);

            //}

           // this.getPaystackData();

           this.isLoading = false;

           alert('Paystack Is Now Activated');

        },


        /**
         * @description deletes Paystack paymentMethod Id from sales_channel_payment_methods
         *  where salesChannel param == sales_channel column in db
         * @param {*} salesChannelId 
         */
        deleteSalesChannelPaymentMethod(salesChannelId){

            this.isLoading = true;

            const payload = [{
                action: 'delete',
                entity: 'sales_channel_payment_method',
                payload: [
                    {
                        salesChannelId: salesChannelId,
                        paymentMethodId: this.paymentMethodId
                    },
                ],
            }];

            this.syncService.sync(payload, {}, { 'single-operation': 1 });

          //  this.getPaystackData();
          this.isLoading = false;

          alert('Paystack Is Now Deactivated');

        }, 
    
       

   },

   

   created() {

        //@description populate views with data
        this.getPaystackData()
       
    
   },

   mounted() {
   // this.assignBooleanValues()
   },

   watch: {

    salesChannels(){
        //console.log(this.paymentMethodId);
        //this.paystackIsDefault();
        this.isLoading = false;
    },

    isLoading(){
        //console.log(`isLoading = ${this.isLoading}`);
    }

   },
    
  
});