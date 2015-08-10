$(window).bind('scroll', function() {
	var scroll = $(window).scrollTop();
	if(scroll > 70){
		var classesMenu = menu.getAttribute('class');
		if(classesMenu.indexOf('menu fixed') == -1){
			classesMenu = classesMenu.replace(' secondary pointing menu', ' menu fixed');
			menu.setAttribute('class', classesMenu);	

			var classesPadding = padding.getAttribute('style');
			this.width = menu.offsetHeight;
			console.log(width);
			classesPadding = classesPadding.replace('padding: 0px', 'padding: ' + this.width + 'px');
			padding.setAttribute('style', classesPadding);

		}
		
	}
	if(scroll == 0){
		var classesMenu = menu.getAttribute('class');
		classesMenu = classesMenu.replace(' menu fixed', ' secondary pointing menu');
		menu.setAttribute('class', classesMenu);

		var classesPadding = padding.getAttribute('style');

		classesPadding = classesPadding.replace('padding: ' + width + 'px', 'padding: 0px');
		padding.setAttribute('style', classesPadding);
		
	}
});