var $isClicked = false;
(function($){
"use strict";
    jQuery(document).on('click','#tu-btn-search',function(e){
        e.preventDefault();
        let url_link    = jQuery('#tu-list-type').find(':selected').attr('data-url');
        jQuery('#tu-header-form').attr('action', url_link).submit();
    });
})(jQuery);

//MOBILE MENU
function tuturnCollapseMenu(responsive){
      
    if (jQuery(window).width() < 1200 && $isClicked === false) {
        $isClicked = true;
        jQuery('.tu-headernav li.menu-item-has-children span,.tu-headernav ul li.page_item_has_children span').on('click', function() {
            jQuery(this).parent('li').toggleClass('tu-menuopen');
        });
        
        jQuery('.tu-headernav li.menu-item-has-children > a, .tu-headernav ul li.page_item_has_children > a').on('click', function() {
            if ( location.href.indexOf("#") != -1 ) {
                jQuery(this).parent('li').toggleClass('tu-menuopen');
            } else{
                //do nothing
            }            
        });  
    }
}

tuturnCollapseMenu(true);
jQuery( window ).resize(function() {
    tuturnCollapseMenu();
});

jQuery(window).load(function () {
    var loading_duration = tuturn_vars.loading_duration;
    jQuery(".preloader-outer").delay(loading_duration).fadeOut();
    jQuery(".sv-preloader-holder").delay(loading_duration).fadeOut("slow");
});

//SVG Render
jQuery("img.amsvglogo").each(function(){var t=jQuery(this),r=t.attr("id"),a=t.attr("class"),e=t.attr("src");jQuery.get(e,function(e){var i=jQuery(e).find("svg");void 0!==r&&(i=i.attr("id",r)),void 0!==a&&(i=i.attr("class",a+" replaced-svg")),i=i.removeAttr("xmlns:a"),t.replaceWith(i)},"xml")});
jQuery('.tu-header .sub-menu-holder').on('click', function() {
    jQuery(this).toggleClass('tu-open-usermenu');
    jQuery('.sub-menu-holder > .sub-menu').slideToggle(300);
}); 

//toggle mobile sidebar
jQuery(".tu-dbmenu").on("click", function () {
    jQuery(".tu-theme-asideholder .tu-asidewrapper , .tu-theme-asideholder .tu-asidedetail").toggleClass("tu-opendbmenu");
});

//MOBILE MENU
function collapseMenu(){
    jQuery('.tu-themenav ul li.menu-item-has-children, .tu-themenav ul li.page_item_has_children').prepend('<span class="tu-dropdownarrow"><i class="icon icon-chevron-right"></i></span>');
    jQuery('.tu-themenav ul li.menu-item-has-children span,.tu-themenav ul li.page_item_has_children .tu-dropdownarrow,.wt-categories-navbar ul li.menu-item-has-children .tu-dropdownarrow').on('click', function() {
        jQuery(this).parent('li').toggleClass('tu-open');
        jQuery(this).next().next().slideToggle(300);
    });        
}
collapseMenu();