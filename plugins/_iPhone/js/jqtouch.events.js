(function($) {
    
    var jQTouchHandler = {
        
        currentTouch : {},

        handleStart : function(e){
            
            jQTouchHandler.currentTouch = {
                startX : event.changedTouches[0].clientX,
                startY : event.changedTouches[0].clientY,
                startTime : (new Date).getTime(),
                deltaX : 0,
                deltaY : 0,
                deltaT : 0
            };

            $(this).bind('touchmove touchend', jQTouchHandler.handle);
            return true;
        },
        
        handle : function(e){
            var touches = event.changedTouches,
            first = touches[0] || null,
            type = '';

            switch(event.type)
            {
                case 'touchmove':
                    jQTouchHandler.currentTouch.deltaX = first.pageX - jQTouchHandler.currentTouch.startX;
                    jQTouchHandler.currentTouch.deltaY = first.pageY - jQTouchHandler.currentTouch.startY;
                    jQTouchHandler.currentTouch.deltaT = (new Date).getTime() - jQTouchHandler.currentTouch.startTime;

                    if (jQTouchHandler.currentTouch.deltaX > jQTouchHandler.currentTouch.deltaY && jQTouchHandler.currentTouch.deltaX > 50 && jQTouchHandler.currentTouch.deltaT < 1000)
                    {
                        $(this).trigger('swiped');
                    }
                    
                    type = 'mousemove';
                break;

                case 'touchend':
                    // event.preventDefault();
                    if (jQTouchHandler.currentTouch.deltaY || jQTouchHandler.currentTouch.deltaX)
                    {

                    }
                    else
                    {
                        type = 'mouseup';
                        $(this).attr('selected', true).trigger('tap');
                    }
                    // $(this).unbind('touchmove touchend');
                    setTimeout(jQTouchHandler.ready, jQTouchHandler.timeDelay);
                    delete currentTouch;
                break;
            }
            if (type != '' && first)
            {
                $(this).trigger(type);
                // return false;
            }
        }
    }

    $.fn.addTouchHandlers = function()
    {
        return this.each(function(i, el){        
            $(el).bind('touchstart', jQTouchHandler.handleStart);  
        });
    }
})(jQuery);
