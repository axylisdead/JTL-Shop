<div class='form-group'>
    <label for="config-{$propname}">{$propdesc.label}</label>
    <textarea name="{$propname}" id="config-{$propname}"
              class="form-control" {if $required}required{/if}>{htmlspecialchars($propval)}</textarea>
    <script>
        tinymce.remove('#config-{$propname}');
        tinymce.init({
            selector: '#config-{$propname}',
            promotion: false,
            branding: false,
            menubar: false,
            relative_urls: false,
            remove_script_host: false,
            document_base_url: window.opc.shopUrl + '/',
            valid_elements: '*[*]',
            skin: 'tinymce-5',
            plugins: 'lists image code emoticons table anchor link',
            language: '{\JTL\Shop::Container()->getGetText()->getLanguage()}'.split('-')[0],
            file_picker_callback: (callback, value, meta) => {
                window.opc.gui.openElFinder((file, mediafilesBaseUrlPath) => {
                    callback(file.url);
                }, 'image');
            },
            toolbar: [
                `
                    bold italic underline strikethrough subscript superscript |
                    bullist numlist |
                    outdent indent |
                    blockquote |
                    alignleft aligncenter alignright alignjustify
                `,
                `
                    anchor image emoticons link table hr | code
                `,
                `
                    blocks fontfamily fontsize forecolor backcolor
                `
            ],
        });

        window.opc.once('save-config', () => {
            $('#config-{$propname}').val(tinymce.get('config-{$propname}').getContent());
        });
    </script>
</div>
