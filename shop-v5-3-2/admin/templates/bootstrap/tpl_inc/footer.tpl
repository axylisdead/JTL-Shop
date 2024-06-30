{if $account}
        <button class="navbar-toggler sidebar-toggler collapsed" type="button" data-toggle="collapse" data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="modal" tabindex="-1" role="dialog" id="modal-footer">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title"></h2>
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

        <div class="modal" tabindex="-1" role="dialog" id="modal-footer-delete-confirm">
            <div id="modal-footer-delete-confirm-default-title" class="d-none">{__('defaultDeleteConfirmTitle')}</div>
            <div id="modal-footer-delete-confirm-default-submit" class="d-none">{__('delete')}</div>
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title"></h2>
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="ml-auto col-sm-6 col-xl-auto mb-2">
                                <button type="button" id="modal-footer-delete-confirm-yes" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash-alt"></i> {__('delete')}
                                </button>
                            </div>
                            <div class="col-sm-6 col-xl-auto">
                                <button type="button" class="btn btn-outline-primary btn-block" data-dismiss="modal">
                                    {__('cancelWithIcon')}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
</div>{* /backend-wrapper *}
{$finderURL = $adminURL|cat:'/'|cat:JTL\Router\Route::ELFINDER}
<script>
    function initTinyMCE() {
        tinymce.remove('textarea.tinymce');
        tinymce.init({
            selector: 'textarea.tinymce',
            promotion: false,
            branding: false,
            menubar: false,
            relative_urls: false,
            remove_script_host: false,
            document_base_url: '{$shopURL}/',
            valid_elements: '*[*]',
            skin: isDarkMode() ? 'oxide-dark' : 'tinymce-5',
            content_css: isDarkMode() ? 'dark' : 'default',
            plugins: 'lists image code emoticons table anchor link',
            language: '{\JTL\Shop::Container()->getGetText()->getLanguage()}'.split('-')[0],
            file_picker_callback: (callback, value, meta) => {
                openElFinder((file, mediafilesBaseUrlPath) => {
                    console.log(file.url);
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
    }
    document.addEventListener('colorThemeChanged', initTinyMCE);
    initTinyMCE();

    $('.select2').select2();
    $(function() {
        ioCall('notificationAction', ['update'], undefined, undefined, undefined, true);
    });

    $( document ).scroll(function () {
        $('[name="scrollPosition"]').val(window.scrollY);
    });

    {if !empty($scrollPosition)}
        var scrollPosition = '{$scrollPosition}';
        $('html, body').animate({
            scrollTop: $("html").offset().top + scrollPosition
        }, 1000);
    {/if}
</script>

{/if}
</body></html>
