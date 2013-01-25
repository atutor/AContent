/**
 * @author Alexey Novak
 * @copyright Copyright © 2013, AContent, All rights reserved.
 */

/*global jQuery*/
/*global AContent */

(function () {
    "use strict";
    
    /**
    * Function which would assign a slide function to links which have a specified class
    * @options
    *    collapsibleElements        - jQuery links which would have a click event bind to them
    *    notCollapsedClass          - class which will be added/removed to the div area which will have a slide effect
    *    collapsibleAreaSelector    - selector which would be used to get all collapsing areas
    *    speed                      - animation speed
    * @author    Alexey Novak
    */
    var makeCollapsibles = function(options) {
        options = options || {};
        
        var collapsibleElements = options.collapsibleElements,
            collapsibleAreaSelector = options.collapsibleAreaSelector,
            collapsibleAreas = collapsibleElements.parent().siblings(collapsibleAreaSelector);
        
        if (!collapsibleElements || collapsibleElements.length === 0) {
            return;
        }
        
        // Bind the click event
        collapsibleElements.click(function (event) {
            var link = (event.currentTarget) ? $(event.currentTarget) : $(event.srcElement),
                collapsibleArea = link.parent().siblings(collapsibleAreaSelector);
            
            collapseWork(collapsibleAreas, collapsibleArea, options.notCollapsedClass, options.speed);
            
            link.focus();
            return false;
        });
        
        /**
        * Function which is executed upon the link click. It will either hide the related area OR show the area and hide all other ones
        * @param    all areas which could be collapsed
        * @param    element which relates to the link being clicked
        * @param    class which will be added or removed depending on the situation
        * @param    animation speed
        * @author    Alexey Novak
        */
        var collapseWork = function(collapsibleAreas, element, notCollapsedClass, speed) {
            if (element.hasClass(notCollapsedClass)) {
                collapse(element, notCollapsedClass, speed);
            } else {
                collapseAll(collapsibleAreas, notCollapsedClass, speed);
                uncollapse(element, notCollapsedClass, speed);
            }
        };
        
        /**
        * Function which will collapse all areas
        * @param    array of jQuery elements which will be collapsed
        * @param    class which will be removed from those areas
        * @param    animation speed
        * @author    Alexey Novak
        */
        var collapseAll = function(collapsibleAreas, notCollapsedClass, speed) {
            $.each(collapsibleAreas.filter("." + notCollapsedClass), function(index, element) {
                collapse($(element), notCollapsedClass, speed);
            });
        };
        
        /**
        * Function which will collapses one element
        * @param    jQuery element which will be collapsed
        * @param    class which will be removed from this element
        * @param    animation speed
        * @author    Alexey Novak
        */
        var collapse = function(element, notCollapsedClass, speed) {
            if (!element.hasClass(notCollapsedClass)) {
                return;
            }
            
            var topRow = element.siblings('.topRow')
            topRow.find('.showLabel').show();
            topRow.find('.hideLabel').hide();
            
            element.slideUp(speed, "linear", function () {
                element.removeClass(notCollapsedClass);
                element.hide();
            });
        };
        
        /**
        * Function which will show the area and convert from collapsed to be displayed one
        * @param    element which will be shown
        * @param    class which will be added from this element
        * @param    animation speed
        * @author    Alexey Novak
        */
        var uncollapse = function (element, notCollapsedClass, speed) {
            if (element.hasClass(notCollapsedClass)) {
                return;
            }
            
            var topRow = element.siblings('.topRow')
            topRow.find('.showLabel').hide();
            topRow.find('.hideLabel').show();
            
            element.addClass(notCollapsedClass);
            element.slideDown(speed, "linear", function () {
                element.show();
            });
        }
    };
    
    /**
    * Function to be called upon page load
    * @author    Alexey Novak
    */
    var initialize = function() {
        var collapsibleElements = $(".collapsible_link");
        
        makeCollapsibles({
            collapsibleElements: collapsibleElements,
            notCollapsedClass: "notcollapsed",
            collapsibleAreaSelector: ".collapsible",
            speed: "300"
        });
    };

    jQuery(document).ready(initialize);
})();