## TinyMCE Uploader

Плагин для TinyMCE4 и Evolution CMS, позволяющий вставлять изображения, путем их перетаскивания на панель редактора. Изображения сохраняются на сервере, в папке assets/images/content/{Y-m}/{d}/, где Y, m, d - год, месяц, день. После загрузки ссылка на картинку вставляется в редактор в позиции курсора. Можно загружать сразу несколько картинок. Важно, чтобы настройки TinyMCE4 не препятствовали сохранению атрибута "data-tmu", который добавляется к тегу img при вставке загруженного изображения.

Незадействованные изображения удаляются по истечении времени, указанного в параметре "lifetime" плагина TinyMCEUploader (по умолчанию 24 часа).

По умолчанию разрешено загружать файлы в форматах png, gif, jpeg.

### Установка и настройка

После установки необходимо в конфигурации системы в разделе "Интерфейс и представление" - "TinyMCE4 Настройки" выбрать тему "Индивидуальная" и в список плагинов, указанный в разделе "Индивидуальные плагины" добавить плагин "tmuploader". Можно также создать свою тему, на основе имеющихся в папке assets/plugins/tinyMCE4/theme/, добавить плагин и выбрать эту тему в системных настройках.

### Расширенная настройка

Более тонкая настройка плагина может быть выполнена путем редактирования параметра Custom Parameters плагина TinyMCE4. Для этого следует добавить туда массив с необходимыми настройками, например:
```
tmuploader: {
    url: "/assets/plugins/tmuploader/ajax.php",
    imageAutoOrientation: true,
    imageTransform: {
        "maxWidth": 400,
        "maxHeight": 300
    },
    filterFn:function(file, info){
        //только jpeg, gif, png
        return /jpeg|gif|png$/.test(file.type);
    },
    prepare: function (file/**Object*/, options/**Object*/){
        if (file.type !== 'image/jpeg' && file.type !== 'image/png') {
            options.imageTransform = false; //запрет обработки файлов кроме jpeg и png;
        }
    },
    completeCallback:function(){
        alert('test');
    }
}
```
Настройки:
* url - файл обработчика на сервере;
* imageAutoOrientation - автоматический поворот по EXIF;
* imageTransform - преобразование изображения до загрузки;
* filterFn - функция для фильтрации файлов до загрузки;
* completeCallback - функция, которая выполняется после загрузки файлов.

Описание параметров imageAutoOrientation, imageTransform, filterFn, prepare содержится в документации к библиотеке [https://github.com/mailru/FileAPI](FileAPI).

Обработку изображений на сервере можно выполнять с помощью плагина на событие OnFileBrowserUpload. В плагине доступны переменные: "invokedBy" со значением "TinyMCEUploader", "filepath" - путь к файлу, "filename" - имя файла.

