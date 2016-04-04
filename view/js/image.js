var zimg = null;
var cont = null;

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
        

        var html = '<div id="image_frame">' +
            '<img src="'+img_src+'" />' +
            '<div id="image_frame_controls1" class="image_frame_controls"><div id="ifc_lt" class="ifc">↖</div><div class="spacer"></div><div id="ifc_rt" class="ifc">↗</div><div class="spacer"></div>' +
            '<div id="ifc_ct" class="ifc">o</div><div class="spacer"></div><div id="ifc_lb" class="ifc">↙</div><div class="spacer"></div><div id="ifc_rb" class="ifc">↘</div></div>' +
            '<div id="image_frame_controls2" class="image_frame_controls"><div id="ifc_fit" class="ifc">fit</div><div id="ifc_100" class="ifc">100%</div><div id="ifc_larger" class="ifc">+</div><div id="ifc_smaller" class="ifc">-</div>' +
            '</div>' +
            '</div>';
        
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
        cont = $('#img-container');

        zimg.draggable();

        // by default fit to container.
        fitToContainer();
    });
}


$(document).on('click','#ifc_larger',function() {
    var width =  zimg.width();
    var height =  zimg.height();

    zimg.width(width * 1.5 );
    zimg.height(height * 1.5 );
    center_img();
});

$(document).on('click','#ifc_smaller',function() {
    var width =  zimg.width();
    var height =  zimg.first().height();

    zimg.width(width * 0.75 );
    zimg.height(height * 0.75 );
    center_img();
});


$(document).on('click','#ifc_100',function() {
    var theImage = new Image();
    theImage.src =  zimg.attr("src")

    zimg.width(theImage.width);
    zimg.height(theImage.height);

    center_img();
});

function fitToContainer() {
    var theImage = new Image();
    theImage.src =  zimg.attr("src");

    var cw = cont.width();
    var ch = cont.height();

    var rw = theImage.width / cw;
    var rh = theImage.height / ch;

    if(rw>rh) {
        zimg.width(cw);
        zimg.height(theImage.height * (1/rw));
    } else {
        zimg.height(ch);
        zimg.width(theImage.width * (1/rh));
    }

    center_img();
};

$(document).on('click','#ifc_fit', fitToContainer);

function center_img() {
    var cw = cont.width();
    var ch = cont.height();

    var iw = zimg.width();
    var ih = zimg.height();

    zimg.css({left: (cw/2)-(iw/2), top: (ch/2)-(ih/2) });
}


$(document).on('click','#ifc_ct',function() {
    center_img();
});

$(document).on('click','#ifc_lt',function() {
    zimg.css({left: 0, top: 0 });
});

$(document).on('click','#ifc_rt',function() {
    zimg.css({left: $('#img-container').width()-zimg.width(), top: 0 });
});

$(document).on('click','#ifc_lb',function() {
    zimg.css({left: 0, top: $('#img-container').height()-zimg.height() });
});

$(document).on('click','#ifc_rb',function() {
    zimg.css({left: cont.width()-zimg.width(), top: cont.height()-zimg.height() });
});