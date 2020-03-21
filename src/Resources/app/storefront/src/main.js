import MoorlMerchantFinder from './moorl-merchant-finder/moorl-merchant-finder-leaflet';

const PluginManager = window.PluginManager;
PluginManager.register('MoorlMerchantFinder', MoorlMerchantFinder, '.cms-element-moorl-merchant-finder');

if (module.hot) {
    module.hot.accept();
}
