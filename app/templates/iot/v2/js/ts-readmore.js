/**
 * Created by Sagar Khatri on 28/09/2016.
 */

(function($){
    $.fn.tsReadMore = function (options) {
        if(typeof options.itemSelector == 'undefined' || typeof options.readMoreSelector == 'undefined') return;
        var settings = $.extend({openItemsNo: 10},options),
            itemsLength = ($(this).find(" > " + settings.itemSelector).length);
        if(!itemsLength) return;
        var hiddenItems = $(this).find(" > " + settings.itemSelector + ":gt("+ (settings.openItemsNo - 1) +")");
        hiddenItems.hide();        
        $(settings.readMoreSelector).click(function () {
            hiddenItems.show();            
        	$(settings.readMoreSelector).hide();
        });

    }
})(jQuery);