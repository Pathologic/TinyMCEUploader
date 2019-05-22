(function(window, $, api){
    var defaults = {
        editor:null,
        filterFn:function(file, info){
            return /jpeg|gif|png$/.test(file.type);
        },
        completeCallback:function(){

        }
    };
    function TinyMCEUploader (options) {
        this._options = $.extend({},defaults,options);
        this._errorCount = 0;
        this._currentFile = 0;
        this._xhr = null;
        this._progressbar = null;
        return this.init();
    }
    TinyMCEUploader.prototype = {
        init: function() {
            var self = this;
            var editor = self._options.editor;
            var dndArea = $(editor.getDoc());
            api.event.dnd(dndArea[0], function (over){
                var container = $(editor.getContainer());
                if (over) {
                    container.css('opacity', '.3');
                } else {
                    container.css('opacity', 1);
                }
            }, function(files){
                if (self._xhr !== null) return;
                self.prepare(files);
            });

            return this;
        },
        prepare:function(files){
            var self = this;
            if (typeof self._options.filterFn === 'function') {
                api.filterFiles(files, self._options.filterFn, function (files, rejected) {
                    self.upload(files);
                });
            } else {
                self.clear();
            }
        },
        upload:function(files) {
            var self = this;
            if( files.length ){
                var options = {
                    files: { file: files },
                    beforeupload: function(xhr/**Object*/, options/**Object*/) {
                        self.clear();
                        var editor = self._options.editor;
                        self._progressbar = editor.windowManager.open({
                            title: 'Загрузка',
                            resizable : false,
                            maximizable : false,
                            body: [
                                {
                                    type: 'progress',
                                    name: 'tmuprogress',
                                    width:200,
                                    height:60
                                }
                            ],
                            buttons: [],
                            onClose: function() {
                                if (self._xhr !== null) self._xhr.abort();
                            }
                        });
                    },
                    progress: function (evt/**Object*/, file/**Object*/, xhr/**Object*/, options/**Object*/){
                        var part = Math.floor(evt.loaded / evt.total * 100);
                        self._progressbar.find('#tmuprogress').value(part);
                    },
                    filecomplete: function(err/**String*/, xhr/**Object*/, file/**Object/, options/**Object*/) {
                        var error = false;
                        if(err === false) {
                            var response;
                            try {
                                response = $.parseJSON(xhr.response);
                            } catch (error) {
                                response = {
                                    success:false,
                                    message:'Не удалось обработать ответ сервера'
                                }
                            }
                            if (!response.success) {
                                error = response.message;
                            }
                        } else {
                            error = 'Ошибка сервера ' + err;
                        }
                        var editor = self._options.editor;
                        if (error !== false) {
                            editor.notificationManager.open({
                                text: error,
                                type: 'error'
                            });
                        } else {
                            editor.insertContent('<img src="' + response.item.file + '" data-tmu="' + response.item.id + '" />');
                        }
                    },
                    complete: function (err, xhr){
                        self.clear();
                        if (typeof self._options.completeCallback === 'function') {
                            self._options.completeCallback();
                        }
                    }
                };
                this._xhr = api.upload($.extend({},this._options,options));
            }
        },
        clear:function(){
            this._xhr = null;
            if (this._progressbar !== null) {
                this._progressbar.close();
                this._progressbar = null;
            }
        }
    };
    window.TinyMCEUploader = TinyMCEUploader;
})(window, jQuery, FileAPI);

