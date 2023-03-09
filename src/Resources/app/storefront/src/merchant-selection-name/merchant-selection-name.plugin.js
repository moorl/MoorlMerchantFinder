import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import queryString from 'query-string';

export default class MoorlMerchantSelectionNamePlugin extends Plugin {
    static options = {
        nameUrl: null,
        initiator: null,
    };

    init() {
        const httpClient = new HttpClient(window.accessKey, window.contextToken);

        httpClient.get(`${this.options.nameUrl}?${queryString.stringify({
            initiator: this.options.initiator
        })}`, (response) => {
            if (response.length) {
                this.el.innerHTML = response;
            }
        });
    }
}
