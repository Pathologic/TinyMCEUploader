tinymce.PluginManager.add('tmuploader', function (editor) {
    editor.on('init', function () {
        var options = {};
        var defaults = {
            url: '/assets/plugins/tmuploader/ajax.php',
            imageAutoOrientation: false
        };
        var options = tinymce.extend(defaults, editor.settings.tmuploader || {});
        options.editor = editor;
        new TinyMCEUploader(options);
    });
    return {
        getMetadata: function () {
            return  {
                name: 'TinyMCE Uploader for Evolution CMS',
                url: 'https://github.com/Pathologic/TinyMCEUploader'
            };
        }
    };
});
