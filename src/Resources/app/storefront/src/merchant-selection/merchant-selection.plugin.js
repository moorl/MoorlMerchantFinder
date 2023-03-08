import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import queryString from 'query-string';

export default class MoorlMerchantSelectionPlugin extends Plugin {
    static options = {
        searchUrl: null,
        pickUrl: null,
        initiator: null,
    };

    init() {
        this._client = new HttpClient(window.accessKey, window.contextToken);
        this._listingWrapper = this.el.querySelector('.js-listing-wrapper');
        this._listingFilter = this.el.querySelector('.js-listing-filter');

        this._registerEvents();
    }

    _registerEvents() {
        this._listingFilter.querySelectorAll("input,select").forEach((el) => {
            ['keyup', 'change', 'force'].forEach(evt =>
                el.addEventListener(evt, () => {
                    this._selectionSearch();
                }, false)
            );
        });
    }

    _selectionSearch() {
        const formData = new FormData(this.el);
        const query = {};
        formData.forEach(function (value, key) {
            query[key] = value;
        });
        query.initiator = this.options.initiator;

        this._client.get(`${this.options.searchUrl}?${queryString.stringify(query)}`, (response) => {
            this._listingWrapper.innerHTML = response;
            window.PluginManager.initializePlugins();
        });
    }
}
