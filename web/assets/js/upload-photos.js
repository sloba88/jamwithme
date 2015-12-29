'use strict';

/* global Routing */
/* global addMessage */
/* global _ */

_.templateSettings.variable = 'rc';
var que = [];
var imageTemplate = _.template($('#imageTemplate').html());
var imageCropModalTemplate = _.template($('#imageCropModalTemplate').html());

function resetPhotosCountIndicator() {
    $('.panel-photos-header .badge span').text($('.profile-media-wall-item').length);
}

function clearCoords(node ){
    node.find('input').val('');
}

function cloneCanvas(oldCanvas) {
    //create a new canvas
    var newCanvas = document.createElement('canvas');
    var context = newCanvas.getContext('2d');

    //set dimensions
    newCanvas.width = oldCanvas.width;
    newCanvas.height = oldCanvas.height;

    //apply the old canvas to the new one
    context.drawImage(oldCanvas, 0, 0);

    //return the new canvas
    return newCanvas;
}

function editCoords(c){
    var context = $('#imageCropModal');
    context.find('input.x_cord').val(c.x);
    context.find('input.y_cord').val(c.y);
    context.find('input.w_cord').val(c.w);
    context.find('input.h_cord').val(c.h);
}

$(function () {

    $('.start-upload').on('click', function(e){
        e.preventDefault();
        if (que.length > 0){
            $.each(que, function(q, e){
                e.submit();
            });
        }
        que = [];
    });

    $('#upload_images').fileupload({
        dataType: 'json',
        autoUpload: false,
        url: Routing.generate('upload_user_image'),
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 5000000, // 5 MB
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        previewMaxWidth: 800,
        previewMaxHeight: 800,
        imageMaxWidth: 800,
        imageMaxHeight: 800,
        previewMinWidth: 75,
        previewMinHeight: 75,
        disableExifThumbnail: true,
        imageCrop: false,
        previewCrop: false
    }).on('fileuploadadd', function (e, data) {
        data.context = $('<div class="preview-container" />').appendTo('#files');
        $.each(data.files, function () {
            var node = $('<p/>');
            node.appendTo(data.context);
        });

        $('.start-upload').show();

    }).on('fileuploadsubmit', function (e, data) {
        var inputs = data.context.find(':input');
        if (inputs.filter(function () {
            return !this.value && $(this).prop('required');
        }).first().focus().length) {
            data.context.find('button').prop('disabled', false);
            return false;
        }
        data.formData = inputs.serializeArray();
    }).on('fileuploadprocessalways', function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);

        que.push(data);

        if (file.preview) {
            node.prepend('<br>')
                .prepend(file.preview);

            $(file.preview).wrap('<a class="preview-thumb" data-index="' + (que.length - 1) + '"></a>');

            setTimeout(function(){
               if (que.length === 1) {
                   $('.preview-thumb').click();
               }
            }, 500 );

        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger" />').text(file.error));
        }

    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        var $progressBar = $('#progress .progress-bar');
        $('#progress').show();
        $progressBar.css(
            'width',
            progress + '%'
        );
        if (progress==100){
            setTimeout(function(){
                //window.location.reload();
                $progressBar.css(
                    'width',
                    0 + '%'
                );
                $('#progress').hide();
            }, 2000 );
        }
    }).on('fileuploaddone', function (e, data) {

        var file = data.result.files;
        if (file.url) {
            $('.no-images-yet').remove();
            $('.profile-media-wall').append(imageTemplate( file ) );
            //$('#files').html('');

            setTimeout(function(){
                $('.profile-media-wall').isotope( 'reloadItems' ).isotope();

                if (jQuery().fancybox) {
                    $('.fancybox').fancybox();
                }

                resetPhotosCountIndicator();

            }, 800);

            if ( que.length === 0 ) {
                $('.start-upload').hide();
                $('#files').html('');
            }


        } else if (file.error) {
            var error = $('<span class="text-danger"/>').text(file.error);
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        }
    }).on('fileuploadfail', function (e, data) {
        addMessage(data.jqXHR.responseJSON.success, data.jqXHR.responseJSON.message);
        $.each(data.files, function (index) {
            var error = $('<span class="text-danger"/>').text('File upload failed.');
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');

    $('body').on('click', '.remove-image-ajax', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var image = $('*[data-image-id="' + id + '"]');
        $.ajax({
            url: Routing.generate('remove_user_image', {'id': id})
        }).done(function( data ) {
            if (data.status == 'success') {
                image.fadeOut(400, function() {
                    image.remove();
                    $('.profile-media-wall').isotope( 'reloadItems' ).isotope();
                });
                addMessage(data.status, data.message);
                $.fancybox.close();
                resetPhotosCountIndicator();
            }
        });
    });

    $('body').on('click', '.set-profile-photo', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var url = Routing.generate('set_avatar', {'id': id});
        $.ajax({
            url: url
        }).done(function(data) {
            if (data.status == 'success') {
                var src = $('.profile-info .user-image').attr('src');
                $('.profile-info .user-image').attr('src', src + '?' +new Date().getTime());
                addMessage(data.status, data.message);
            }
        });
    });

    $('body').on('click', '.preview-thumb', function() {
        var index = $(this).data('index');
        var file = que[index];
        var newImage = cloneCanvas(file.files[0].preview);
        var node;
        var self = $(this);

        $('body').append(imageCropModalTemplate());

        var imageCropModal = $('#imageCropModal');
        imageCropModal.find('.modal-body').html(newImage);
        imageCropModal.find('.modal-dialog').width(newImage.width + 40);
        imageCropModal.find('.modal-dialog').css('minWidth', 460);

        imageCropModal.modal({
            'backdrop': 'static',
            'show' : true
        });

        imageCropModal.on('hidden.bs.modal', function () {
            imageCropModal.remove();
            //re index others
            $('.preview-thumb').each(function(k){
                $(this).attr('data-index', k);
            });
        });

        imageCropModal.on('shown.bs.modal', function () {
            var jcropApi;
            imageCropModal.find('canvas').Jcrop({
                onChange:   editCoords,
                onSelect:   editCoords,
                onRelease:  clearCoords,
                minSize: [100, 100]
            },function(){
                jcropApi = this;
            });

            node = $('<p class="crop-coords"/>')
                .append('<input type="text" size="4" class="x_cord" id="x1_0" name="x1[]" />')
                .append('<input type="text" size="4" class="y_cord" id="y1_0" name="y1[]" />')
                .append('<input type="text" size="4"  class="w_cord" id="w_0" name="w[]" />')
                .append('<input type="text" size="4"  class="h_cord"id="h_0" name="h[]" />');

            node.appendTo(imageCropModal.find('.modal-body'));

        });

        imageCropModal.find('.btn-save-changes').on('click', function(){
            node.appendTo(file.context);
            file.submit();
            imageCropModal.modal('hide');
            self.parents('.preview-container').remove();
            que.splice(index, 1);
        });

        imageCropModal.find('.btn-remove-crop-photo').on('click', function(){
            imageCropModal.modal('hide');
            self.parents('.preview-container').remove();
            que.splice(index, 1);
        });

    });

});