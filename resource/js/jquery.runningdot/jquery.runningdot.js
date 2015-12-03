(function($) {
    
    $.runningDot = function(el, options) {
        var base = this;
        base.$el = $(el);
                
        base.$el.data("runningDot", base);
        base.dotItUp = function($element, maxDots) {
            if ($element.text().length == maxDots) {
                $element.text("");
            } else {
                $element.append(".");
            }
        };
        
        base.stopInterval = function() {    
            clearInterval(base.theInterval);
        };
        
        base.init = function() {
            
            if ( typeof( options.speed ) === "undefined" || options.speed === null ) options.speed = 300;
            if ( typeof( options.maxDots ) === "undefined" || options.maxDots === null ) options.maxDots = 3;
            if ( typeof( options.message ) === "undefined" || options.message === null ) options.message = "Loading";


            base.speed = options.speed;           
            base.maxDots = options.maxDots;           
            base.message = options.message;
            
            base.options = $.extend({},$.runningDot.defaultOptions, options);
                                 
            base.$el.html("<span>"+options.message+"<em></em></span>");
            
            base.$dots = base.$el.find("em");
            base.$loadingText = base.$el.find("span");
            
            base.$el.css("position", "relative");
            base.$loadingText.css({
                "position": "absolute",
                "top": (base.$el.outerHeight() / 2) - (base.$loadingText.outerHeight() / 2),
                "left": (base.$el.width() / 2) - (base.$loadingText.width() / 2)
            });
                        
            base.theInterval = setInterval(base.dotItUp, base.options.speed, base.$dots, base.options.maxDots);
            
        };
        
        base.init();
    
    };
    
    $.runningDot.defaultOptions = {
        speed: 300,
        maxDots: 3,
        message:"Loading"
    };
    
    $.fn.runningDot = function(options) {
        if (typeof(options) == "string") {
            var safeGuard = $(this).data('runningDot');
            if (safeGuard) {
                safeGuard.stopInterval();
            }
        } else { 
            return this.each(function(){
                (new $.runningDot(this, options));
            });
        } 
        
    };
    
})(jQuery);
