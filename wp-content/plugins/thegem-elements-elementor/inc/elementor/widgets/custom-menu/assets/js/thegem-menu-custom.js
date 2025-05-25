(function($) {
	$(function() {
		$(document).on('click', '.thegem-menu-custom.thegem-menu-custom--clickable ul.nav-menu-custom a' ,function(e) {
			var $link = $(this);
			var $menuItem = $link.closest('li');
			var $subMenu = $('> ul', $menuItem);

			if($subMenu.length) {
				event.preventDefault();
				var $subMenus = $('> ul', $menuItem);
				if($menuItem.hasClass('collapsed')) {
					$subMenus.slideUp();
					$menuItem.removeClass('collapsed');
				} else {
					if($menuItem.hasClass('clickable') && $('li.show-parent', $subMenus).length === 0) {
						$subMenus.prepend('<li class="show-parent"><a href="' + $link.attr('href') + '">' + thegemCustomMenuOptions.showCurrentLabel + '<i class="indicator"></i></a></li>');
					}
					$subMenu.slideDown();
					$menuItem.addClass('collapsed');
				}
			}
		});
	});
})(jQuery);