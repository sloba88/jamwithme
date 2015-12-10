$(function () {

    var que = [];

    $('.start-upload').on('click', function(e){
        e.preventDefault();
        if (que.length > 0){
            $.each(que, function(q, e){
                e.submit();
            });
        }

        que = [];
    });

    var imageTemplate = _.template($('#imageTemplate').html());

    var imageIndex = 0;
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
        disableExifThumbnail: true,
        imageCrop: false,
        previewCrop: false
    }).on('fileuploadadd', function (e, data) {
        data.context = $('<div class="preview-container" />').appendTo('#files');

        $.each(data.files, function (index, file) {
            var node = $('<p/>')
                .append('<input type="text" size="4" class="x_cord" id="x1_'+imageIndex+'" name="x1[]" />')
                .append('<input type="text" size="4" class="y_cord" id="y1_'+imageIndex+'" name="y1[]" />')
                .append('<input type="text" size="4" class="x2_cord" id="x2_'+imageIndex+'" name="x2[]" />')
                .append('<input type="text" size="4" class="y2_cord" id="y2_'+imageIndex+'" name="y2[]" />')
                .append('<input type="text" size="4"  class="w_cord" id="w_'+imageIndex+'" name="w[]" />')
                .append('<input type="text" size="4"  class="h_cord"id="h_'+imageIndex+'" name="h[]" />');
            imageIndex++;
            que.push(data);
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
        if (file.preview) {
            node.prepend('<br>')
                .prepend(file.preview);

            console.log(file);

            $(file.preview).wrap( "<a class='preview-thumb'></a>");

            var modalCropContainer = $('#imageCropModalTemplate').clone();
            var newImage = cloneCanvas(file.preview);
            modalCropContainer.find('.modal-body').html(newImage);
            modalCropContainer.find('.modal-dialog').width(newImage.width+40);
            node.append(modalCropContainer);

            $(file.preview).on('click', function(){
                modalCropContainer.modal('show');
            });

            modalCropContainer.on('shown.bs.modal', function (e) {
                var jcrop_api;
                $(newImage).Jcrop({
                    onChange:   showCoords,
                    onSelect:   showCoords,
                    onRelease:  clearCoords,
                    minSize: [100, 100]
                },function(){
                    jcrop_api = this;
                });
            });

            modalCropContainer.find('.cancel-crop').on('click', function(){
                clearCoords();
            });

            function showCoords(c){
                node.find('input.x_cord').val(c.x);
                node.find('input.y_cord').val(c.y);
                node.find('input.x2_cord').val(c.x2);
                node.find('input.y2_cord').val(c.y2);
                node.find('input.w_cord').val(c.w);
                node.find('input.h_cord').val(c.h);
            };

            function clearCoords(){
                node.find('input').val('');
            };

        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger"/>').text(file.error));
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
            },2000)
        }
    }).on('fileuploaddone', function (e, data) {

        var file = data.result.files;
        if (file.url) {
            $('.no-images-yet').remove();
            $('.profile-media-wall').append(imageTemplate( file ) );
            $('#files').html('');

            setTimeout(function(){
                $('.profile-media-wall').isotope( 'reloadItems' ).isotope();

                if (jQuery().fancybox) {
                    $('.fancybox').fancybox();
                }

            }, 500);

            $('.start-upload').hide();

        } else if (file.error) {
            var error = $('<span class="text-danger"/>').text(file.error);
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        }
    }).on('fileuploadfail', function (e, data) {
        $.each(data.files, function (index, file) {
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
});

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