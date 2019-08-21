// Import all necessary Storefront plugins and scss files
import MoorlMerchantFinder from './moorl-merchant-finder/moorl-merchant-finder';

// Register them via the existing PluginManager
const PluginManager = window.PluginManager;
PluginManager.register('MoorlMerchantFinder', MoorlMerchantFinder, '[data-moorl-merchant-finder]');

// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}
