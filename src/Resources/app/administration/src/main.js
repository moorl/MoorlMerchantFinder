import './module/moorl-merchant-finder';
import './module/moorl-merchant-marker';
import './module/sw-cms';
import './extension';

const SearchTypeService = Shopware.Service('searchTypeService');

SearchTypeService.upsertType('moorl_merchant', {
    entityName: 'moorl_merchant',
    /*entityService: 'moorlMerchantService',*/
    placeholderSnippet: 'moorl-merchant-finder.general.placeholderSearchBar',
    listingRoute: 'moorl.merchant.finder.list'
});
