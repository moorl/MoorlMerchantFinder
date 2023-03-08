import MoorlMerchantSelectionPlugin from './merchant-selection/merchant-selection.plugin';
import MoorlMerchantSelectionPickPlugin from './merchant-selection-pick/merchant-selection-pick.plugin';

const PluginManager = window.PluginManager;
PluginManager.register('MoorlMerchantSelection', MoorlMerchantSelectionPlugin, '[data-moorl-merchant-selection]');
PluginManager.register('MoorlMerchantSelectionPick', MoorlMerchantSelectionPickPlugin, '[data-moorl-merchant-selection-pick]');

if (module.hot) {
    module.hot.accept();
}
