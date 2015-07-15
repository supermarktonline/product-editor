$(document).on('click','#thumb-container > div',function() {
    
    
    setActiveImage($(this).attr('data-src'));
    
    
});


function setActiveImage(img_src) {
    
    // 1. Preload the image
    var tmpImg = new Image();
    
    tmpImg.src=img_src;
    
    $(tmpImg).on('load',function(){
        
        var origWidth = tmpImg.width;
        var origHeight = tmpImg.height;
      
        var container_width = $('#current_image_wrapper').width()-20;
        var container_height = 600;
        
        var preview_height = (container_width * origHeight) / origWidth;
        
        
        // 2. If height / width not bigger than container, just display it, otherwise apply easyzooom
        var easyzoom = "";
        
        if(origWidth > container_width || origHeight > container_height) {
            easyzoom = "easyzoom";
        }

        var html = '<div id="current_image" class="'+easyzoom+'">';
        html += '<a id="zoom_image_link" href="'+img_src+'" target="_blank">';
        html += '<img id="zoom_image_overview" src="'+img_src+'" alt="" />';
        html += '</a>';
        html += '</div>';
        
        if(preview_height<580) {
            $('#current_image_wrapper').height(preview_height+20);
            $('#img-container').height(preview_height+20);
        } else {
            $('#current_image_wrapper').height(600);
            $('#img-container').height(600);
        }
        
        $('#current_image_wrapper').html(html);

        $('#zoom_image_overview').css('max-height',container_height);
        $('#zoom_image_overview').css('max-width',container_width);

        if(easyzoom === "easyzoom") {
            $('.easyzoom').easyZoom();
        }
    });
    
    
    

}