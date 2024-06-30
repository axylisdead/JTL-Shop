{$src = $instance->getProperty($propdesc.srcProp)}

<input type="hidden" id="config-{$propname}" name="{$propname}" value="{htmlentities(json_encode($propval))}"
       data-prop-type="json">

{if empty($src)}
    {$imgsrc = null}
{else}
    {$imgsrc = \JTL\Shop::getURL()|cat:'/'|cat:$smarty.const.STORAGE_OPC|cat:$src}
{/if}

<div {if empty($imgsrc)}style="display: none"{/if} id="banner-editor-{$propname}">
    <div class="form-group">
        <label for="config-{$propname}">{$propdesc.label}</label>
        <div style="position: relative">
            <img src="{$imgsrc}" alt="Banner Zones"
                 id="banner-image-{$propname}" class="img-fluid w-100">
            <div id="banner-zones-{$propname}" class="banner-zones"></div>
        </div>
    </div>

    <div class="form-group">
        <button type="button" class="opc-btn-primary opc-medium-btn" id="banner-add-zone">{__('zoneNew')}</button>
        <button type="button" class="opc-btn-secondary opc-medium-btn" id="banner-del-zone" style="display: none">
            {__('zoneDelete')}
        </button>
    </div>

    <div id="zone-props-{$propname}" style="display: none" class="zone-props">
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control" id="zone-title-{$propname}" placeholder="{__('title')}">
            </div>
            <div class="col-6">
                <input type="text" class="form-control" id="zone-url-{$propname}" placeholder="{__('url')}">
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control" id="zone-class-{$propname}" placeholder="{__('cssClass')}">
            </div>
            <div class="col-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="zone-product-{$propname}" placeholder="{__('products')}">
                    <div class="input-group-append">
                        <button type="button" class="btn primary" id="banner-del-product">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <input type="checkbox" class="form-control" id="zone-target-{$propname}" value="1">
                    <label for="zone-target-{$propname}">
                        {__('targetBlank')}
                    </label>
                </div>
            </div>
        </div>
        <textarea class="form-control" id="zone-desc-{$propname}" placeholder="{__('description')}"></textarea>
    </div>
</div>

<div id="banner-zone-blueprint-{$propname}" class="banner-zone" style="display: none;">
    <div id="banner-zone-resizer-blueprint-{$propname}"
         class="zone-resizer"></div>
</div>

<script>
    (function () {
        let nextZoneId    = 0;
        let hiddenInput   = $('#config-{$propname}');
        let bannerEditor  = $('#banner-editor-{$propname}');
        let bannerImg     = $('#banner-image-{$propname}');
        let bannerZones   = $('#banner-zones-{$propname}');
        let zoneBlueprint = $('#banner-zone-blueprint-{$propname}');
        let zoneProps     = $('#zone-props-{$propname}');
        let zoneTitle     = $('#zone-title-{$propname}');
        let zoneDesc      = $('#zone-desc-{$propname}');
        let zoneUrl       = $('#zone-url-{$propname}');
        let zoneClass     = $('#zone-class-{$propname}');
        let zoneProduct   = $('#zone-product-{$propname}');
        let zoneTarget    = $('#zone-target-{$propname}');
        let dragging      = false;
        let resizing      = false;
        let editorw       = 0;
        let editorh       = 0;
        let startx        = 0;
        let starty        = 0;
        let draggedZone   = null;
        let selectedZone  = null;
        let startLeft     = 0;
        let startTop      = 0;
        let startWidth    = 0;
        let startHeight   = 0;

        deserializeZones().forEach(function(zoneData) {
            addZone(zoneData);
        });

        function delZone()
        {
            selectedZone.remove();
            zoneProps.hide();
            $('#banner-del-zone').hide();
            serializeZones();
        }

        function addZone(data = null)
        {
            let newZone = zoneBlueprint.clone();
            let resizer = newZone.find('#banner-zone-resizer-blueprint-{$propname}');

            let zoneData = data || {
                zoneId: nextZoneId,
                title: '',
                desc: '',
                url: '',
                class: '',
                target: false,
                productId: 0,
                productName: '',
                left: 50,
                top: 50,
                width: 25,
                height: 25,
            };

            if (data === null) {
                nextZoneId++;
            }

            newZone.show().appendTo(bannerZones);

            newZone
                .attr('id', '')
                .data('zone', zoneData)
                .css({
                    left:   zoneData.left + '%',
                    top:    zoneData.top + '%',
                    width:  zoneData.width + '%',
                    height: zoneData.height + '%',
                })
                .on('mousedown', function(e) {
                    dragging    = true;
                    startx      = e.clientX;
                    starty      = e.clientY;
                    editorw     = bannerZones.width();
                    editorh     = bannerZones.height();
                    draggedZone = newZone;
                    startLeft   = parseFloat(draggedZone[0].style.left);
                    startTop    = parseFloat(draggedZone[0].style.top);
                    selectZone(newZone);

                    e.preventDefault();
                });

            resizer
                .attr('id', '')
                .on('mousedown', function(e) {
                    resizing    = true;
                    startx      = e.clientX;
                    starty      = e.clientY;
                    draggedZone = newZone;
                    editorw     = bannerZones.width();
                    editorh     = bannerZones.height();
                    startWidth  = parseFloat(draggedZone[0].style.width);
                    startHeight = parseFloat(draggedZone[0].style.height);
                    selectZone(newZone);

                    e.preventDefault();
                    e.stopPropagation();
                });

            if (data === null) {
                serializeZones();
                selectZone(newZone);
            }
        }

        function selectZone(zone)
        {
            let zoneData = zone.data('zone');

            selectedZone = zone;
            bannerZones.find('.selected').removeClass('selected');
            selectedZone.addClass('selected');
            zoneProduct.val(zoneData.productName);
            zoneTitle.val(zoneData.title);
            zoneDesc.val(zoneData.desc);
            zoneUrl.val(zoneData.url);
            zoneClass.val(zoneData.class);
            zoneTarget.prop('checked', zoneData.target);
            zoneProps.show();
            $('#banner-del-zone').show();
        }

        function moveSelected(newx, newy)
        {
            let zoneData = draggedZone.data('zone');
            let curw = parseFloat(draggedZone[0].style.width);
            let curh = parseFloat(draggedZone[0].style.height);

            newx = Math.max(0, Math.min(100 - curw, newx));
            newy = Math.max(0, Math.min(100 - curh, newy));

            draggedZone[0].style.left = newx + "%";
            draggedZone[0].style.top  = newy + "%";

            zoneData.left = newx;
            zoneData.top  = newy;

            draggedZone.data('zone', zoneData);
            serializeZones();
        }

        function resizeSelected(neww, newh)
        {
            let zoneData = draggedZone.data('zone');
            let curx = parseFloat(draggedZone[0].style.left);
            let cury = parseFloat(draggedZone[0].style.top);

            neww = Math.max(5, Math.min(100 - curx, neww));
            newh = Math.max(5, Math.min(100 - cury, newh));

            draggedZone[0].style.width  = neww + "%";
            draggedZone[0].style.height = newh + "%";

            zoneData.width  = neww;
            zoneData.height = newh;

            draggedZone.data('zone', zoneData);
            serializeZones();
        }

        function serializeZones()
        {
            let zonesData = [];

            bannerZones.children().each(function(i, zone) {
                zonesData.push($(zone).data('zone'));
            });

            hiddenInput.val(JSON.stringify(zonesData));
        }

        function deserializeZones()
        {
            return JSON.parse(hiddenInput.val());
        }

        function delProduct()
        {
            let zoneData = selectedZone.data('zone');

            zoneProduct.val('');
            zoneData.productId   = 0;
            zoneData.productName = '';
            selectedZone.data('zone', zoneData);
            serializeZones();
        }

        zoneTitle.on('input', function() { changeZoneProp('title', $(this).val()); });
        zoneDesc.on('input', function() { changeZoneProp('desc', $(this).val()); });
        zoneUrl.on('input', function() { changeZoneProp('url', $(this).val()); });
        zoneClass.on('input', function() { changeZoneProp('class', $(this).val()); });
        zoneTarget.on('input', function() { changeZoneProp('target', $(this).prop('checked')); });

        function changeZoneProp(name, val)
        {
            let zoneData = selectedZone.data('zone');
            zoneData[name] = val;
            selectedZone.data('zone', zoneData);
            serializeZones();
        }

        window.opc.enableTypeahead(
            '#zone-product-{$propname}',
            'getProducts',
            'cName',
            null,
            (e, item) => {
                let zoneData = selectedZone.data('zone');
                zoneData.productId   = item.kArtikel;
                zoneData.productName = item.cName;
                selectedZone.data('zone', zoneData);
                serializeZones();
            },
        );

        opc.setImageSelectCallback(function (url, propName, absUrl)
        {
            bannerEditor.show();
            bannerImg.attr('src', absUrl);
        });

        $(document)
            .on('mouseup', function(e) {
                dragging = false;
                resizing = false;
            });

        $(document)
            .on('mousemove', function(e) {
                if (dragging || resizing) {
                    let deltax        = e.clientX - startx;
                    let deltay        = e.clientY - starty;
                    let deltaxpercent = deltax * 100 / editorw;
                    let deltaypercent = deltay * 100 / editorh;

                    if (dragging) {
                        moveSelected(startLeft + deltaxpercent, startTop + deltaypercent);
                        e.preventDefault();
                    } else if(resizing) {
                        resizeSelected(startWidth + deltaxpercent, startHeight + deltaypercent);
                        e.preventDefault();
                    }
                }
            });

        $('#banner-add-zone').on('click', function() { addZone(); });
        $('#banner-del-zone').on('click', delZone);
        $('#banner-del-product').on('click', delProduct)
    })();
</script>
