/**
 * RB AUTOTYPE
 * Makes text with class "rb-autotype" type itself one character at a time
 */

// empty array 
var textContent = [];

// function for detecting whether element is in view
function Utils() {
}

Utils.prototype = {
    constructor: Utils,
    isElementInView: function (element, fullyInView) {
        var pageTop = jQuery(window).scrollTop();
        var pageBottom = pageTop + jQuery(window).height();
        var elementTop = jQuery(element).offset().top;
        var elementBottom = elementTop + jQuery(element).height();

        if (fullyInView === true) {
            return ((pageTop < elementTop) && (pageBottom > elementBottom));
        } else {
            return ((elementTop <= pageBottom) && (elementBottom >= pageTop));
        }
    }
};
var Utils = new Utils();

// function to check elements for visibility when user scrolls
function onScrollFunc() {
    let elems = jQuery('.rb-autotype');
    elems.each(function(index) {
        let selector = '#' + jQuery(this).attr('id');
        var isElementInView = Utils.isElementInView(jQuery(selector), false);
        if (isElementInView && textContent[index][4] == false) {
            autotype(jQuery(this).attr('id'));
            textContent[index][4] = true;
        } 
    });
}

// recurring (Timeout) function to type one more letter of the string
function autotype(id) {

    for (i=0; i < textContent.length; i++) {
        
        if (textContent[i][0] == id) {
            textContent[i][2] += textContent[i][1][textContent[i][3]];
            selector = '#' + textContent[i][0];
            jQuery(selector).text(textContent[i][2]);
            textContent[i][3]++; 
            if (textContent[i][3] < textContent[i][1].length) {
                setTimeout(function() { autotype(id); }, 20); // Speed of type
            }
        }
    }
} 

// initialise everything on page load
 jQuery(document).ready(function() {

    // remove type from autotype elements and save in array
    let elems = jQuery('.rb-autotype');
    elems.each(function(index) {

        //retain width and height of completed element
        let elemHeight = jQuery(this).height();
        let elemWidth = jQuery(this).width();
        jQuery(this).height(elemHeight);
        jQuery(this).width(elemWidth);

        // put text into array
        textContent[index] = [];
        textContent[index][0] = jQuery(this).attr('id'); // ID
        textContent[index][1] = jQuery(this).text().split(''); // Character Array
        textContent[index][2] = ''; // New String
        textContent[index][3] = 0; // Counter
        textContent[index][4] = false; // Has started typing

        // blank out text
        jQuery(this).text('');

        // update on scroll
        jQuery(document).scroll(function() {
            onScrollFunc();
        })
    })

    onScrollFunc();

 })