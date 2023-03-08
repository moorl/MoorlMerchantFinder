import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import CookieStorage from 'src/helper/storage/cookie-storage.helper';
import queryString from 'query-string';

export default class MoorlMerchantSelectionPickPlugin extends Plugin {
    static options = {
        merchantId: null,
        storageKey: 'moorl-merchant-picker_selected',
        isLoading: 'is-loading',
        selectedState: 'd-none',
        notSelectedState: 'is-not-selected',
        pickUrl: null,
        initiator: null,
    };

    init() {
        this.classList = {
            isLoading: this.options.isLoading,
            selectedState: this.options.selectedState,
            notSelectedState: this.options.notSelectedState
        };

        let selectedMerchantId = CookieStorage.getItem(this.options.initiator);
        if (!selectedMerchantId) {
            console.log("Merchant not selected");
            console.log(this.options.initiator);

            console.log(CookieStorage.getItem('moorl-merchant-finder'));
            console.log(CookieStorage.getItem('moorl-merchant-picker'));
        } else if (selectedMerchantId !== this.options.merchantId) {
            this._removeActiveStateClasses();
        } else {
            this._addActiveStateClasses();
        }

        this._registerEvents();
    }

    _registerEvents() {
        this.el.addEventListener('click', event => {
            event.preventDefault();

            const httpClient = new HttpClient(window.accessKey, window.contextToken);

            httpClient.get(`${this.options.pickUrl}?${queryString.stringify({
                initiator: this.options.initiator,
                merchantId: this.options.merchantId,
            })}`, () => {
                CookieStorage.setItem(this.options.initiator, this.options.merchantId);

                //window.location.reload();
            });
        });
    }

    _addActiveStateClasses() {
        this.el.classList.remove(this.classList.notSelectedState);
        this.el.classList.add(this.classList.selectedState);
    }

    _removeActiveStateClasses() {
        this.el.classList.remove(this.classList.selectedState);
        this.el.classList.add(this.classList.notSelectedState);
    }
}
