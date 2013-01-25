/**
 * @author Alexey Novak
 * @copyright Copyright © 2013, AContent, All rights reserved.
 */

/*global jQuery*/
/*global AContent */

(function () {
    "use strict";
    
    var makeCollapsibles = function(options) {
        options = options || {};
        
        var collapsibleElements = options.collapsibleElements,
            notCollapsedClass = options.notCollapsedClass,
            collapsibleAreaSelector = options.collapsibleAreaSelector,
            collapsibleAreas = collapsibleElements.parent().siblings(collapsibleAreaSelector),
            minHeight = options.minHeight;
        
        if (!collapsibleElements || collapsibleElements.length === 0) {
            return;
        }
        
        var collapseWork = function(collapsibleAreas, element, notCollapsedClass) {
            if (element.hasClass(notCollapsedClass)) {
                collapse(element, notCollapsedClass);
            } else {
                collapseAll(collapsibleAreas, notCollapsedClass);
                uncollapse(element, notCollapsedClass);
            }
        };
        
        var collapseAll = function(collapsibleAreas, notCollapsedClass) {
            $.each(collapsibleAreas.filter("." + notCollapsedClass), function(index, element) {
                collapse($(element), notCollapsedClass);
            });
        };
        
        var collapse = function(element, notCollapsedClass) {
            if (!element.hasClass(notCollapsedClass)) {
                return;
            }
            
            element.animate({"height": 0}, 200, "linear", function() {
                element.removeClass(notCollapsedClass);
                element.hide();
            });
        };
        
        var uncollapse = function(element, notCollapsedClass) {
            if (element.hasClass(notCollapsedClass)) {
                return;
            }
            
            element.height("0px");
            element.show();
            element.addClass(notCollapsedClass);
            element.animate({"height": minHeight}, 200, "linear");
        }
        
        // Bind the click event
        collapsibleElements.click(function (event) {
            var link = (event.currentTarget) ? $(event.currentTarget) : $(event.srcElement),
                collapsibleArea = link.parent().siblings(collapsibleAreaSelector);
                
            collapseWork(collapsibleAreas, collapsibleArea, notCollapsedClass);
            
            link.focus();
            return false;
        });
    };
    
    var initialize = function() {
        var collapsibleElements = $(".collapsible_link");
        
        makeCollapsibles({
            collapsibleElements: collapsibleElements,
            notCollapsedClass: "notcollapsed",
            collapsibleAreaSelector: ".collapsible",
            minHeight: "8em"
        });
    };

    jQuery(document).ready(initialize);
})();