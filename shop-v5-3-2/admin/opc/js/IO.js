import {capitalize} from "./utils.js";

export class IO
{
    constructor(jtlToken, adminUrl)
    {
        this.jtlToken = jtlToken;
        this.ioUrl    = adminUrl + '/io';
    }

    async init()
    {
        this.generateIoFunctions(['getIOFunctionNames', 'getPageIOFunctionNames']);
        this.generateIoFunctions(await this.getIOFunctionNames());
        this.generateIoFunctions(await this.getPageIOFunctionNames());
    }

    generateIoFunctions(names)
    {
        for(const name of names) {
            this[name] = this.generateIoFunction('opc' + capitalize(name));
        }
    }

    generateIoFunction(publicName)
    {
        return async (...args) => {
            try {
                let result = await this.ioCall(publicName, ...args);
                opc.emit(publicName + ':resolve', result);
                return result;
            } catch (e) {
                opc.emit(publicName + ':reject', e);
                throw e;
            }
        };
    }

    async ioCall(name, ...params)
    {
        return await this.abortableIoCall(name, undefined, ...params);
    }

    async abortableIoCall(name, abortSignal, ...params)
    {
        let formData = new FormData();
        formData.append('jtl_token', this.jtlToken);
        formData.append('io', JSON.stringify({name, params}));
        let response = await fetch(this.ioUrl, {method: 'POST', body: formData, signal: abortSignal});
        return await response.json();
    }

    createPortlet(portletClass)
    {
        return this.getPortletPreviewHtml({class: portletClass});
    }
}
