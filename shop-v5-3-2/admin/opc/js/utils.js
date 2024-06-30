export const localDateFormat = 'DD.MM.YYYY - HH:mm';
export const internalDateFormat = 'YYYY-MM-DD HH:mm:ss';

class Subject
{
    constructor()
    {
        this.listeners = [];
    }

    on(cb)
    {
        this.listeners.includes(cb) || this.listeners.push(cb);
    }

    off(cb)
    {
        this.listeners.includes(cb) && this.listeners.splice(this.listeners.indexOf(cb), 1);
    }

    once(cb)
    {
        let tmpCB = data => {
            cb(data);
            this.off(tmpCB);
        };

        this.on(tmpCB);
    }

    emit(data)
    {
        this.listeners.slice().forEach(cb => cb(data));
    }
}

export class Emitter
{
    constructor()
    {
        this.subjects = {};
    }

    subject(name)
    {
        return this.subjects[name] = this.subjects[name] || new Subject();
    }

    on(name, cb)
    {
        this.subject(name).on(cb);
    }

    off(name, cb)
    {
        this.subject(name).off(cb);
    }

    once(name, cb)
    {
        this.subject(name).once(cb);
    }

    emit(name, data)
    {
        this.subject(name).emit(data);
    }
}

export function noop() {}

export function installJqueryFixes()
{
    // Fix from: https://gist.github.com/Reinmar/b9df3f30a05786511a42#gistcomment-2897528
    // to ensure Tiny MCE text inputs are focused inside bootstrap modals

    $.fn.modal.Constructor.prototype._enforceFocus = function() {
        let $element = $(this._element);
        $(document)
            .off('focusin.bs.modal')
            .on('focusin.bs.modal', function(e) {
                if ($element[0] !== e.target
                    && !$element.has(e.target).length
                    && !$(e.target).closest('.tox-tinymce-aux').length
                ) {
                    $element.trigger('focus');
                }
            });
    };

    // Fix from: https://stackoverflow.com/questions/5347357/jquery-get-selected-element-tag-name
    // to conveniently get the tag name of a matched element

    $.fn.tagName = function() {
        return this.prop("tagName").toLowerCase();
    };
}

export function capitalize(str)
{
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Query DOM elements, bind handlers available in obj to them and set them as properties to obj
 * @param obj
 * @param elmIds
 */
export function installGuiElements(obj, elmIds)
{
    elmIds.forEach(function(elmId) {
        var elm         = $('#' + elmId);
        var elmVarName  = elmId;
        var handlerName = '';

        if (elm.length === 0) {
            elm         = $('.' + elmId);
            elmVarName  = elmId + 's';
        }

        if (elm.length === 0) {
            console.log('warning: ' + elmId + ' could not be found');
            return;
        }

        if (elm.attr('draggable') === 'true') {
            handlerName = 'on' + capitalize(elmId) + 'DragStart';

            if (obj[handlerName]) {
                elm.off('dragstart').on('dragstart', obj[handlerName]);
            }

            handlerName = 'on' + capitalize(elmId) + 'DragEnd';

            if (obj[handlerName]) {
                elm.off('dragend').on('dragend', obj[handlerName]);
            }

        } else if (elm.tagName() === 'a' || elm.tagName() === 'button') {
            handlerName = 'on' + capitalize(elmId);

            if (obj[handlerName]) {
                elm.off('click').on('click', obj[handlerName]);
            }
        } else if (elm.tagName() === 'form') {
            handlerName = 'on' + capitalize(elmId);

            if (obj[handlerName]) {
                elm.off('submit').submit(obj[handlerName]);
            }
        } else if (elm.tagName() === 'input' && elm.attr('type') === 'checkbox') {
            handlerName = 'on' + capitalize(elmId);

            if (obj[handlerName]) {
                elm.off('click').on('click', obj[handlerName]);
            }
        }

        obj[elmVarName] = elm;
    });
}

export function initDragStart(e)
{
    // firefox needs this
    e.originalEvent.dataTransfer.effectAllowed = 'move';
    e.originalEvent.dataTransfer.setData('text/html', '');
}