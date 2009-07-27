// David Kaneda, jQuery jQTouch extensions


(function($) {
    
    var currentPage = null;
    var currentHash = location.hash;
    var hashPrefix = "#";
    var currentWidth = 0;
    var pageHistory = [];
    var pageHistoryInfo = [];
    var newPageCount = 0;
    var checkTimer;
    var browser = {
        type: navigator.userAgent,
        safari: (/AppleWebKit\/([^\s]+)/.exec(navigator.userAgent) || [,false])[1],
        webkit: (/Safari\/(.+)/.exec(navigator.userAgent) || [,false])[1]
    };

    // Cached elements
    var $body, $head = $('head');

    $.jQTouch = function(options)
    {
        var defaults = {
            fullScreen: true,
            fullScreenClass: 'fullscreen',
            statusBar: 'default', // other options: black-translucent, black
            icon: null,
            iconIsGlossy: false,
            fixedViewport: true,

            // Quick setup selectors 
            // TODO: Replace with dynamic events system $('ul li a').drillDown();
            slideInSelector: 'ul li a',
            slideRightSelector: '',
            backSelector: '.back',
            flipSelector: '.flip',
            slideUpSelector: '.slideup',
            initializeTouch: 'a'
        };        
        var settings = $.extend({}, defaults, options),
            hairextensions;

        if (settings.preloadImages)
        {
            for (var i = settings.preloadImages.length - 1; i >= 0; i--){
                (new Image()).src = settings.preloadImages[i];
            };
        }

        // Set back buttons
        if (settings.backSelector)
        {
            $(settings.backSelector).live('click',function(){
                if (pageHistory[pageHistory.length-2]) 
                    $.jQTouch.showPageById(pageHistory[pageHistory.length-2]);

                return false;
            });
        }

        // Set icon
        if (settings.icon)
        {
            var precomposed = (settings.iconIsGlossy) ? '' : '-precomposed';
            hairextensions += '<link rel="apple-touch-icon' + precomposed + '" href="' + settings.icon + '" />';
        }

        // Set viewport
        if (settings.fixedViewport)
        {
            hairextensions += '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;"/>';
        }

        // Set full-screen
        if (settings.fullScreen)
        {
            hairextensions += '<meta name="apple-mobile-web-app-capable" content="yes" />';

            if (settings.statusBar)
            {
                hairextensions += '<meta name="apple-mobile-web-app-status-bar-style" content="' + settings.statusBar + '" />';
            }
        }
        
        if (hairextensions) $head.append(hairextensions);
        
        // Create an array of the "next page" selectors
        // TODO: DRY
        var liveSelectors = [];
        
        if (settings.slideInSelector) liveSelectors.push(settings.slideInSelector);
        if (settings.slideRightSelector) liveSelectors.push(settings.slideRightSelector);
        if (settings.flipSelector) liveSelectors.push(settings.flipSelector);
        if (settings.slideUpSelector) liveSelectors.push(settings.slideUpSelector);

        // Selector settings
        if (liveSelectors.length > 0)
        {
            $(liveSelectors.join(', ')).live('click',function liveClick(){

                var $el = $(this);
                var hash = $el.attr('hash');
                var transition = 'slideInOut';

                if ($el.is(settings.flipSelector)) transition = 'flip';
                if ($el.is(settings.slideRightSelector)) transition = 'slideRight';
                if ($el.is(settings.slideUpSelector)) transition = 'slideUp';

                if ( hash && hash != '#')
                {
                    if ($(hash).length > 0)
                    {
                        $el.attr('selected', 'true');
                        $.jQTouch.showPage($(hash), transition);
                        setTimeout($.fn.unselect, 250, $el);
                    }
                    else
                    {
                        console.warn('There is no panel with that ID.');
                        $el.unselect();
                        return false;
                    }

                }
                else if ( $el.attr('target') != '_blank' )
                {
                    $el.attr('selected', 'progress');

                    $.jQTouch.showPageByHref($(this).attr('href'), null, null, null, transition, function(){ setTimeout($.fn.unselect, 250, $el) });
                
                    return false;
                }
            });

            // Initialize on document load:
            $(function(){
                
                $body = $('body');
                
                if (settings.fullScreenClass && window.navigator.standalone == true)
                {
                    $body.addClass(settings.fullScreenClass);
                }
                
                if (settings.initializeTouch)
                    $(settings.initializeTouch).addTouchHandlers();

                var page = $('body > *:first');
                if (page) $.jQTouch.showPage(page);
                
                // TODO: Find best way to customize and make event live...
                $('form').submit($.jQTouch.submitForm);

                $.jQTouch.startCheck();
            })

        }
    }
    
    $.fn.transition = function(css, options) {
      
      var $el = $(this);
      
      var defaults = {
          speed : '250ms',
          callback: null,
          ease: 'ease-in-out',
      };

      var settings = $.extend({}, defaults, options);
      
      if(settings.speed === 0) { // differentiate 0 from null
          $el.css(css);
          window.setTimeout(callback, 0);
      } else {
          var s = [];
          
          for(var i in css) s.push(i);
          $el.css({ webkitTransitionProperty: s.join(", "), webkitTransitionDuration: settings.speed, webkitTransitionTimingFunction: settings.ease });
          if (settings.callback) $el.one('webkitTransitionEnd', settings.callback);
          
          setTimeout(function(el){ el.css(css) }, 0, $el);

          return this;
        }
      }
    
    $.jQTouch.checkOrientAndLocation = function()
    {
        if (window.innerWidth != currentWidth)
        {   
            currentWidth = window.innerWidth;
            currentHeight = window.innerHeight;

            var orient = currentWidth < currentHeight ? "profile" : "landscape";

            $body.trigger('orientChange', orient).removeClass('profile landscape').addClass(orient);

            setTimeout(scrollTo, 0, 0, 20);
        }
        
        if (location.hash != currentHash && $(location.hash).length == 1)
        {
            $.jQTouch.showPageById(location.hash);
        }
        else
        {
            location.hash = currentHash;
        }
            
    }
    
    $.jQTouch.showPage = function( page, transition, backwards )
    {
        if (page)
        {
            var fromPage = currentPage;
            currentPage = page;

            if (fromPage)
                $.jQTouch.animatePages(fromPage, page, transition, backwards);
            else
                $.jQTouch.updatePage(page, fromPage, transition);
        }
    }
    
    $.jQTouch.showPageById = function( hash )
    {
        var page = $(hash);
        
        if (page)
        {
            var transition;
            var currentIndex = pageHistory.indexOf(currentHash);
            var index = pageHistory.indexOf(hash);
            var backwards = index != -1;

            if (backwards) {
                transition = pageHistoryInfo[currentIndex].transition;
                
                pageHistory.splice(index, pageHistory.length);
                pageHistoryInfo.splice(index, pageHistoryInfo.length);                
            }
            
            $.jQTouch.showPage(page, transition, backwards);
        }
    }
    
    $.jQTouch.insertPages = function( nodes, transition )
    {
        var targetPage;
        
        nodes.each(function(index, node){
            
            if (!$(this).attr('id'))
                $(this).attr('id', (++newPageCount));
                
            $(this).appendTo($body);
            
            if ( $(this).attr('selected') == 'true' || ( !targetPage && !$(this).hasClass('btn')) )
                targetPage = $(this);
        });
        
        if (targetPage) $.jQTouch.showPage(targetPage, transition);
        
    }

    $.jQTouch.showPageByHref = function(href, data, method, replace, transition, cb)
    {

        $.ajax({
            url: href,
            data: data,
            type: method || "GET",
            success: function (data, textStatus)
            {

                $('a[selected="progress"]').attr('selected', 'true');
                
                if (replace) $(replace).replaceWith(data);
                else
                {
                    $.jQTouch.insertPages( $(data), transition );
                }
                
                if (cb) cb(true);
            },
            error: function (data)
            {
                if (cb) cb(false);
            }
        });

    }
    
    $.jQTouch.submitForm = function()
    {
        $.jQTouch.showPageByHref($(this).attr('action') || "POST", $(this).serialize(), $(this).attr('method'));
        return false;
    }
    
    $.jQTouch.animatePages = function(fromPage, toPage, transition, backwards)
    {
        clearInterval(checkTimer);
        
        toPage.trigger('pageTransitionStart', { direction: 'out' });
        fromPage.trigger('pageTransitionStart', { direction: 'out' });
        
        var callback = function(event){
            $.jQTouch.updatePage(toPage, fromPage, transition);
            fromPage.attr('selected', 'false');
            $.jQTouch.startCheck();
            toPage.trigger('pageTransitionEnd', { direction: 'in' });
	        fromPage.trigger('pageTransitionEnd', { direction: 'out' });
        }

        if (transition == 'flip'){
            toPage.flip({backwards: backwards});
            fromPage.flip({backwards: backwards, callback: callback});
        }
        else if (transition == 'slideUp')
        {
            if (backwards)
            {
                toPage.attr('selected', true);
                fromPage.slideUpDown({backwards: backwards, callback: callback});
            }
            else
            {
                toPage.slideUpDown({backwards: backwards, callback: callback});
            }
        }
        else if (transition == 'slideRightSelector')
        {
            
        }
        else
        {
            toPage.slideInOut({backwards: backwards, callback: callback});
            fromPage.slideInOut({backwards: backwards});
        }
        
    }
    
    $.jQTouch.startCheck = function()
    {
        checkTimer = setInterval($.jQTouch.checkOrientAndLocation, 250);
    }
    
    $.jQTouch.updatePage = function(page, fromPage, transition)
    {
        if (page)
        {
            if (!page.attr('id'))
                page.attr('id', (++newPageCount));

            location.replace(hashPrefix + page.attr('id'));
            currentHash = location.hash;

            var existingIndex = pageHistory.indexOf(currentHash);

            pageHistory.push(currentHash);

            var trans = (existingIndex == -1) ? transition : pageHistoryInfo[existingIndex];

            pageHistoryInfo.push({page: page, transition: trans});
        }
    }
    
    $.fn.unselect = function(obj)
    {
        obj = obj || $(this);
        obj.attr('selected', false);
    }
    
    $.fn.flip = function(options)
    {
        return this.each(function(){
            var defaults = {
                direction : 'toggle',
                backwards: false,
                callback: null
            };

            var settings = $.extend({}, defaults, options);

            var dir = ((settings.direction == 'toggle' && $(this).attr('selected') == 'true') || settings.direction == 'out') ? 1 : -1;
            
            if (dir == -1) $(this).attr('selected', 'true');
            
            $(this).parent().css({webkitPerspective: '600'});
            
            $(this).css({
                '-webkit-backface-visibility': 'hidden',
                '-webkit-transform': 'rotateY(' + ((dir == 1) ? '0' : (!settings.backwards ? '-' : '') + '180') + 'deg)'
            }).transition({'-webkit-transform': 'rotateY(' + ((dir == 1) ? (settings.backwards ? '-' : '') + '180' : '0') + 'deg)'}, {callback: settings.callback});
        })
    }
    
    $.fn.slideInOut = function(options)
    {
        var defaults = {
            direction : 'toggle',
            backwards: false,
            callback: null
        };

        var settings = $.extend({}, defaults, options);
        
        return this.each(function(){

            var dir = ((settings.direction == 'toggle' && $(this).attr('selected') == 'true') || settings.direction == 'out') ? 1 : -1;                
            // Animate in
            if (dir == -1){

                $(this).attr('selected', 'true')
                    .find('h1, .button')
                        .css('opacity', 0)
                        .transition({'opacity': 1})
                        .end()
                    .css({'-webkit-transform': 'translateX(' + (settings.backwards ? -1 : 1) * currentWidth + 'px)'})
                    .transition({'-webkit-transform': 'translateX(0px)'}, {callback: settings.callback})
                        

            }
            // Animate out
            else
            {
                $(this)
                    .find('h1, .button')
                        .transition( {'opacity': 0} )
                        .end()
                    .transition(
                        {'-webkit-transform': 'translateX(' + ((settings.backwards ? 1 : -1) * dir * currentWidth) + 'px)'}, { callback: settings.callback});
            }
        })
    }
    
    $.fn.slideUpDown = function(options)
    {
        var defaults = {
            direction : 'toggle',
            backwards: false,
            callback: null
        };

        var settings = $.extend({}, defaults, options);
        
        return this.each(function(){

            var dir = ((settings.direction == 'toggle' && $(this).attr('selected') == 'true') || settings.direction == 'out') ? 1 : -1;                
            // Animate in
            if (dir == -1){

                $(this).attr('selected', 'true')
                    .css({'-webkit-transform': 'translateY(' + (settings.backwards ? -1 : 1) * currentHeight + 'px)'})
                    .transition({'-webkit-transform': 'translateY(0px)'}, {callback: settings.callback})
                        .find('h1, .button')
                        .css('opacity', 0)
                        .transition({'opacity': 1});
            }
            // Animate out
            else
            {
                $(this)
                    .transition(
                        {'-webkit-transform': 'translateY(' + currentHeight + 'px)'}, {callback: settings.callback})
                    .find('h1, .button')
                        .transition( {'opacity': 0});
            }

        })
    }

})(jQuery);