import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';
import frFR from './snippet/fr-FR.json';

import './page/paystack-settings-page';
//import './page/component/paystack-input-field';


Shopware.Module.register('paystack-settings', {

    type: 'plugin',
    name: 'heading.name',
    title: 'heading.title',
    description: 'heading.description',
    color: '#62ff80',
    icon: 'default-money-wallet',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB,
        'fr-FR': frFR
    },

    routes: {
        index: {
            component: 'paystack-settings-page',
            path: 'index'
        },
    },

    /*navigation: [{
        label: 'Paystack Settings',
        color: '#62ff80',
        path: 'paystack.settings.index',
        parent: 'sw-catalogue',
        position: -100,
        icon: 'default-object-lab-flask'
    }],*/

    settingsItem: {
        group: 'plugins',
        to: 'paystack.settings.index',
        icon: 'default-money-wallet',
        backgroundEnabled: true,
        color: '#28a745',
    }

   
});






