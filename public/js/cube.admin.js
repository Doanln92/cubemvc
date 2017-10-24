function createJS(d, s, src, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) { return; }
    js = d.createElement(s);
    js.id = id;
    js.src = src;
    fjs.parentNode.insertBefore(js, fjs);
};

jQuery(function($) {
    // 
    if ($('#post-content') && window.tinymce_url != undefined) {
        createJS(document, 'script', tinymce_url + '/jquery.tinymce.min.js', 'tinymce-min2');
        createJS(document, 'script', tinymce_url + '/tinymce.js', 'tinymce-min3');

    }

    if ($('.custom-file-input')) {
        $('.custom-file-input').change(function() {
            if (this.files.length > 0) {
                var filename = this.files[0].name;
                var txtFile = $(this).parent().find('.custom-file-control');
                if (txtFile) {
                    $(txtFile).html(filename);
                } else {
                    $(txtFile).html("");
                }
            }

        });
    }

    if ($('.inp-date')) {
        $('.inp-date').datetimepicker({
            locale: 'vi',
            format: 'YYYY-MM-DD'
        });
    }

    var gallery_total = 1;
    $('#button-add-gallery-item').click(function(){
        var item = '<hr><div id="galery-file-'+gallery_total+'" class="gallery-add-image">';
            item +='<div class="gallery-image-file">';
            item +='<input type="file" name="gallery_file_'+gallery_total+'" class="form-control">';
            
            item +='</div>';
            item +='<div class="gallery-image-text">';
            item +='<input type="text" name="gallery_text['+gallery_total+']" class="form-control" placeholder="Chú thích (tùy chọn)">';
            item +='</div>';
            item +='<div class="gallery-image-file">';
            item +='<input type="text" name="gallery_link['+gallery_total+']" class="form-control" placeholder="Liên kết (tùy chọn)">';
            item +='</div>';
            item +='</div>';
            
        $('#gallery-upload').append(item).animate({scrollTop:9999},500);

        gallery_total++;
        $('#gallery_total').val(gallery_total);
    });
    $('.btn-remove-gallery').click(function(){
        var id = $(this).attr('data-id');
        $.ajax({
            url: dashboard_url+'/products/delete-gallery',
            type:"POST",
            data: {id:id},
            cookie: true,
            success:function(response){
                console.log(response);
                if(response!=0 && response.length < 15){
                    $('#gallery-item-'+response).remove();
                }
            },
            error:function(){
                console.log('error');
            }
        });
    });
    
});