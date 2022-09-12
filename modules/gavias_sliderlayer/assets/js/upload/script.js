(function($) {
    $(function(){
        // Initialize the jQuery File Upload plugin
        $('.upload').each(function(){
            var $this = $(this);
            var $_id = $(this).attr('data-id');
            $(this).fileupload({
                add: function (e, data) {
                    var $_id = data.form[0].id;
                    $('#gva-' + $_id + ' .loading').each(function(){
                            $(this).css('display', 'inline-block'); 
                    });
                    var jqXHR = data.submit().done(function(data){
                        data = JSON.parse(data);
                        $('#gva-' + $_id + ' .loading').each(function(){
                            $(this).css('display', 'none'); 
                        });
                        $('#gva-' + $_id + ' input.file-input').each(function(){
                            $(this).val(data['file_url']); 
                        });

                        $('#gavias_slider_single').css('background-image', 'url(\'' + data['file_url_full'] + '\')');

                        $('#gva-' + $_id + ' .gavias-field-upload-remove').each(function(){
                            $(this).css('display', 'inline-block');
                        });

                    });
                },

                progress: function(e, data){
                
                },
                fail: function(e, data){
                    // Something has gone wrong!
                    data.context.addClass('error');
                }
            });
        });

        $('.upload-image-layer').each(function(){
            var $this = $(this);
            var $_id = $(this).attr('data-id');
            $(this).fileupload({
                add: function (e, data) {

                    var $_id = data.form[0].id;

                    $('#gva-' + $_id + ' .loading').each(function(){
                            $(this).css('display', 'inline-block'); 
                    });
                    var jqXHR = data.submit().done(function(data){
                        data = JSON.parse(data);
                        $('#gva-' + $_id + ' .loading').each(function(){
                            $(this).css('display', 'none'); 
                        });
                        $('#gva-' + $_id + ' input.file-input').each(function(){
                            $(this).val(data['file_url']); 
                            $('#g-image-layer').trigger('onchange');
                        });
                    });
                },

                progress: function(e, data){
                
                },
                fail: function(e, data){
                    // Something has gone wrong!
                    data.context.addClass('error');
                }
            });
        });

        // Helper function that formats the file sizes
        function formatFileSize(bytes) {
            if (typeof bytes !== 'number') {
                return '';
            }

            if (bytes >= 1000000000) {
                return (bytes / 1000000000).toFixed(2) + ' GB';
            }

            if (bytes >= 1000000) {
                return (bytes / 1000000).toFixed(2) + ' MB';
            }

            return (bytes / 1000).toFixed(2) + ' KB';
        }

    });

    $(document).ready(function () {
        //Gavias Load Images
        function gavias_sliderlayer_load_images($btn, $btn_choose_image){
            $($btn).click(function(){
                $this = $(this);
                $.ajax({
                 url: drupalSettings.gavias_sliderlayer.get_images_upload_url,
                 type: 'POST',
                 success: function (data) {
                    
                    var html = '';
                    $.each(data['data'], function( index, value ) {
                        if(value['file_url_full'] != 'undefined' || value['file_url_full']){
                            html += '<a data-image="'+value['file_url']+'" data-image-demo="'+value['file_url_full']+'" class="'+$btn_choose_image+'" ><img src="'+value['file_url_full']+'"/></a>';
                        }
                    });
                   $this.parents('.gva-upload-image').find('.gavias-box-images .gavias-box-images-inner .list-images').each(function(){
                        $(this).html(html);
                        $(this).parents('.gavias-box-images').addClass('open-popup');
                    })
                },
                 error: function (jqXHR, textStatus, errorThrown) {
                   alert(textStatus + ":" + jqXHR.responseText);
                 }
              });
            });  
        }

        //Gavias Choose Image Slider
        function gavias_sliderlayer_choose_image_slider(){

            $( ".gva-upload-image" ).delegate( ".btn-choose-image-upload", "click", function() {
                var file_url = $(this).attr('data-image');
                var file_url_full = $(this).attr('data-image-demo');
                $(this).parents('.gva-upload-image').find('input.file-input').each(function(){
                    $(this).val(file_url); 
                });

                $('#gavias_slider_single').css('background-image', 'url(\'' + file_url_full + '\')');

                $(this).parents('.gavias-box-images').removeClass('open-popup');
            });


            $('.gva-upload-image .close').click(function(){
                $(this).parents('.gavias-box-images').removeClass('open-popup');
            });
        }

        //Gavias Choose Image Layer
        function gavias_sliderlayer_choose_image_layer(){

            $( ".gva-upload-image-layer" ).delegate( ".btn-choose-image-upload-layer", "click", function() {
                var file_url = $(this).attr('data-image');
                var file_url_full = $(this).attr('data-image-demo');
                $(this).parents('.gva-upload-image').find('input.file-input').each(function(){
                    $(this).val(file_url); 
                });

                $('#g-image-layer').trigger('onchange');

                $(this).parents('.gavias-box-images').removeClass('open-popup');
            });


            $('.gva-upload-image .close').click(function(){
                $(this).parents('.gavias-box-images').removeClass('open-popup');
            });
        }



        gavias_sliderlayer_load_images('.btn-get-images-upload', 'btn-choose-image-upload');
        gavias_sliderlayer_load_images('.btn-get-images-upload-layer', 'btn-choose-image-upload-layer');
        gavias_sliderlayer_choose_image_slider();
        gavias_sliderlayer_choose_image_layer();
    });

    $(document).ready(function(){
        $('.gavias-field-upload-remove').click(function(){
          $(this).parent().find('.gavias-image-demo').attr('src', $(this).attr("data-src"));
          $(this).parent().find('input.file-input').val('');
        })
    });

})(jQuery);