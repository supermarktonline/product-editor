var zimg = null;

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
        var container_height = 800;
        
        var preview_height = (container_width * origHeight) / origWidth;
        

        // 2. If height / width not bigger than container, just display it, otherwise apply easyzooom
        var easyzoom = "";

        if(origWidth > container_width || origHeight > container_height) {
            easyzoom = "easyzoom";
        }

        var html = '<div id="image_frame"><img src="'+img_src+'" /><div id="image_frame_controls"><div id="ifc_center">center</div><div id="ifc_fit">fit</div><div id="ifc_100">100%</div><div id="ifc_larger">+</div><div id="ifc_smaller">-</div></div></div>';
        
        if(preview_height<780) {
            $('#current_image_wrapper').height(preview_height+20);
            $('#img-container').height(preview_height+20);
        } else {
            $('#current_image_wrapper').height(800);
            $('#img-container').height(800);
        }
        
        $('#current_image_wrapper').html(html);

        $('#zoom_image_overview').css('max-height',container_height);
        $('#zoom_image_overview').css('max-width',container_width);


        zimg = $('#image_frame img').first();

        zimg.draggable();

    });
}


$(document).on('click','#ifc_larger',function() {
    var width =  zimg.width();
    var height =  zimg.height();

    zimg.width(width * 1.5 );
    zimg.height(height * 1.5 );
});

$(document).on('click','#ifc_smaller',function() {
    var width =  zimg.width();
    var height =  zimg.first().height();

    zimg.width(width * 0.75 );
    zimg.height(height * 0.75 );
});


$(document).on('click','#ifc_100',function() {
    var theImage = new Image();
    theImage.src =  zimg.attr("src")

    zimg.width(theImage.width);
    zimg.height(theImage.height);

    center_img();
});

$(document).on('click','#ifc_fit',function() {
    var theImage = new Image();
    theImage.src =  zimg.attr("src");

    var cw = $('#img-container').width();
    var ch = $('#img-container').height();

    var rw = theImage.width / cw;
    var rh = theImage.height / ch;

    if(rw>rh) {
        zimg.width(cw);
        zimg.height(theImage.height * (1/rw));

        zimg.css({top:0, left: (cw/2)-(theImage.width * (1/rh))/2 });
    } else {
        zimg.height(ch);
        zimg.width(theImage.width * (1/rh));

        zimg.css({top:0, left: (cw/2)-(theImage.width * (1/rh))/2 });
    }

});

$(document).on('click','#ifc_center',function() {
    center_img();
});

function center_img() {
    var cw = $('#img-container').width();
    var ch = $('#img-container').height();

    var iw = zimg.width();
    var ih = zimg.height();

    zimg.css({left: (cw/2)-(iw/2), top: (ch/2)-(ih/2) });
}