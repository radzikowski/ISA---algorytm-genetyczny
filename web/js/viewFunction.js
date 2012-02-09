ViewFunction = Backbone.Model.extend({
    defaults: {
        'particles' : []
    },
    initialize: function(){
    	
    	var model = this;
        ile = 1000000;
        t=0;
        Crafty.bind('EnterFrame', function(e){
            if ((e.frame % 10 == 0 ) && (ile <= t)) {
                model.moveParticles();
                ile++;
            }
        })

    	$('#calculate').live('click', function(){
            Crafty.scene("loading");
    	});
    },
    'calculate' : function(){
    	var model = this;
    	a = $('.a-value input').val(); // get from inputs
    	b = $('.b-value input').val(); 
    	var precision = $('.precision-value input').val();
    	var n = $('.n-value input').val();
        t = $('.t-value input').val();
        //globalne zmienne
    	c1 = $('.c1-value input').val();
        c2 = $('.c2-value input').val();
        c3 = $('.c3-value input').val();
        bi = 0;
        neighbourR = $('.neighbour-r-value input').val();
        neighbourPercent = $('.neighbour-percent-value input').val();
        geneticAlgorithm = new GeneticAlgorithm({ 'a' : a, 'b' : b, 'precision' : precision }); //global

        this.clear();
        this.drawOXOY();
    	for(var i=0; i<n; i++){
    		model.calcEvaluationFunction(i+1, a, b, precision);
    	}

        ile = 1;
        move = true;
    },
    'moveParticles': function(){
        var model = this,
            particles = model.get('particles');

        _.each(particles, function(particle){
            particle.nextTime();
        });
  
    },
    'calcEvaluationFunction' : function(lp, a, b, precision){
    	var model = this;
    	
    	var xReal = geneticAlgorithm.randomXReal();
    	var xInt = geneticAlgorithm.calcXIntFromReal(xReal);
    	var xBin = geneticAlgorithm.calcXBinFromInt(xInt);
    	var valFunc = geneticAlgorithm.calcEvaluationFunctionFromReal(xReal);
    	model.insert(lp, xReal, xInt, xBin, valFunc);
    },
    'insert' : function(lp, xReal, xInt, xBin, valFunc){
        var model = this,
            pos = this.getCraftyPositionFor(xReal, valFunc),
            particles = model.get('particles');

        particles.push(new Particle({ 'x' : pos.x, y : pos.y, bg:  pos.y , xReal: xReal, fx : valFunc}));

        model.set({'particles': particles });
    },
    'drawOXOY' : function(){
        var model = this;

        var posOXOY = model.getCraftyPositionFor(0, 0);
        Crafty.e("2D, DOM, Color")
            .attr({x : posOXOY.x, y: 0, w: 1, h: Crafty.viewport.height })
            .color('#363636');

        Crafty.e("2D, DOM, Text")
            .attr({x : posOXOY.x-10, y: 0})
            .text('OY 2');
        
        Crafty.e("2D, DOM, Text")
            .attr({x : posOXOY.x- 15, y: Crafty.viewport.height-20 })
            .text('-2');

        Crafty.e("2D, DOM, Color")
            .attr({x : 0, y: posOXOY.y, w: Crafty.viewport.width, h: 1 })
            .color('#363636');
        
        Crafty.e("2D, DOM, Text")
            .attr({x : 0, y: posOXOY.y -16 })
            .text('OX  '+( $('.a-value input').val()) );

        Crafty.e("2D, DOM, Text")
            .attr({x : Crafty.viewport.width-15, y: posOXOY.y +5 })
            .text(' '+( $('.b-value input').val()) );

    },
    'clear' : function(){
        var model = this,
            particles = [];

        model.set({'particles': particles });
    },
    getCraftyPositionFor: function(x, y){
        var w = Crafty.viewport.width,
            h = Crafty.viewport.height,
            top = 2,
            down = -2;

        y = 0;

        x = (w / (b-a)) * (x - a);
        y = (h / (top-down)) * (y - down);
        return {x: x, y: y };
    }
});