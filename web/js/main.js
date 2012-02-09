window.onload = function() {
	
	require([
		'/web/js/viewFunction.js?v='+new Date().valueOf()+'',
		'/web/js/geneticAlgorithm.js?v='+new Date().valueOf()+''
	], function(){
		viewFunction = new ViewFunction();
		geneticAlgorithm = null;

		sceneW = 700;
		sceneH = 250;
		Crafty.init(sceneW, sceneH);

		Crafty.scene("loading", function() {
	        sc = [];			
			var textPosition = 150,
				loadingText = Crafty.e("2D, DOM, Text")
					.attr({w: 500, h: 20, x: ((Crafty.viewport.width-500) / 2), y: (Crafty.viewport.height / 2) + textPosition, z: 2})
					.text('Ładownie')
					.css({"text-align": "center", 'color' : '#FFF', 'font-size' : '24px'});

		    require(['/web/js/entities/particle.js?v='+new Date().valueOf()+''], function(){
		    	loadingText.destroy();
		   		Crafty.scene('chart');
		   	});
		});


		Crafty.scene("chart", function() {
			Crafty.background('yellow');
			viewFunction.calculate();
		});

		Crafty.scene("loading");
		
	});

}