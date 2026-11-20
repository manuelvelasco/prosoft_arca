/*Tinymce Init*/


$(window).on('load', function () {
    "use strict";

    setTimeout(function () {
        if (typeof tinymce !== 'undefined') {
            tinymce.remove('.tinymce');

            tinymce.init({
                selector: '.tinymce',
                height: 300,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code'
                ],
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                setup: function (editor) {
                    editor.on('init', function () {
                        editor.setMode('design');
                        editor.getBody().setAttribute('contenteditable', true);
                    });
                }
            });
        }
    }, 500); // Da tiempo a que Fancybox muestre el iframe
});
