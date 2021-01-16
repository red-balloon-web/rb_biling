/**
 * RB WORDPRESS MENUS
 */

// open and close modal menu
document.querySelector('#rb-hamburger, #rb-hamburger *').addEventListener("click", openModalMenu);
document.querySelector('#rb-modal-menu__close-button, #rb-modal-menu__close-button *').addEventListener("click", closeModalMenu);
function openModalMenu() {
  document.getElementById('rb-modal-menu').classList.add('open');
}
function closeModalMenu() {
  document.getElementById('rb-modal-menu').classList.remove('open');
}

// fade in and out sub menu items on desktop
jQuery('.rb-desktop-navigation__menu ul > li').hover(function() {
  jQuery(this).children('ul').fadeIn(500);
}, function() {
  jQuery(this).children('ul').fadeOut();
});

// modal menu submenus
jQuery('#rb-modal-menu li.menu-item-has-children ul').hide();
jQuery('#rb-modal-menu li.menu-item-has-children > a').click(function() {
  jQuery(this).next().slideToggle();
  jQuery(this).toggleClass('closed-link');
});
jQuery('#rb-modal-menu li.menu-item-has-children > a').addClass('sub-link closed-link');