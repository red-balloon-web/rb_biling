/**
 * RB VERTICAL EQUALISER
 * Add to the rb_elements array element selectors which you want at the same height and the breakpoint you want it above
 * For example adding ['.excerpt', 992] will equalise all elements with class 'excerpt' above 992px
 */

jQuery(document).ready(function() {

    var rbveq_elements = [];
    var rbveq_breakpoints = [];

    // select everything with rbveq

    let allElems = jQuery('.rbveq');

    // find first rbveq--* selector and add to array if not already there

    allElems.each(function(index) {
        var classList = jQuery(this).attr('class').split(/\s+/);
        classList.forEach(function(item, index) {

            if (item.startsWith('rbveq--')) {
                console.log (item);

                // put it in the array if it's not already there

                if (!rbveq_elements.includes(item)) {
                    rbveq_elements.push(item);

                    let breakpoint = 768;
                    // check whether a breakpoint is specified

                    let allGroup = jQuery(`.${item}`);
                    allGroup.each(function(index) {
                        var subClassList = jQuery(this).attr('class').split(/\s+/);
                        subClassList.forEach(function(item, index) {
                            if (item.startsWith('rbveq-breakpoint--')) {
                                var matches = item.match(/(\d+)/); 
                                if (matches) { 
                                    breakpoint = parseInt(matches[0]); 
                                } 
                            }
                        })
                    });

                    rbveq_breakpoints.push(breakpoint);
                }
            }
        });
        console.log(rbveq_elements);
        console.log(rbveq_breakpoints);
    });



    const rb_viewport_width = jQuery(document).width();

    rbveq_elements.forEach(setHeight);

    function setHeight(item, index) {

        let rb_top_height = 0;
        jQuery(`.${rbveq_elements[index]}`).each(function(index) {
            let elem_height = jQuery(this).height();
            if (elem_height > rb_top_height) {
                rb_top_height = elem_height;
            }   
        });

        console.log(`element: ${rbveq_elements[index]} height: ${rb_top_height}`);

        if (rb_viewport_width >= rbveq_breakpoints[index]) {
            jQuery(`.${rbveq_elements[index]}`).height(rb_top_height);
        }
    }

});
