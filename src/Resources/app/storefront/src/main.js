import MoorlMerchantSelectionPlugin from './merchant-selection/merchant-selection.plugin';
import MoorlMerchantSelectionPickPlugin from './merchant-selection-pick/merchant-selection-pick.plugin';
import MoorlMerchantSelectionNamePlugin from './merchant-selection-name/merchant-selection-name.plugin';

const PluginManager = window.PluginManager;
PluginManager.register('MoorlMerchantSelection', MoorlMerchantSelectionPlugin, '[data-moorl-merchant-selection]');
PluginManager.register('MoorlMerchantSelectionPick', MoorlMerchantSelectionPickPlugin, '[data-moorl-merchant-selection-pick]');
PluginManager.register('MoorlMerchantSelectionName', MoorlMerchantSelectionNamePlugin, '[data-moorl-merchant-selection-name]');

if (module.hot) {
    module.hot.accept();
}
