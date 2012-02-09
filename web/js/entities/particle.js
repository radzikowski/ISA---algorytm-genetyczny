Particle = Backbone.Model.extend({
    defaults: {
    	'v' : 0,
    },
    initialize: function(){
    	
    	var model = this,
            b = $('.b-value inout').val(),
            a = $('.a-value inout').val(),
            r = Crafty.viewport.width / (b-a) * neighbourR,
    		entity = Crafty.e("2D, DOM, particle, Color, Collision")
    			.attr({x: this.get('x'), y: this.get('y')-5, w: 10, h: 10, 'xReal': this.get('xReal'), 'fx': this.get('fx'), 'bg': this.get('fx') })
                .color('red');

    	model.set({ 'entity' : entity});
    },
    getEntity: function(){
    	return this.get('entity');	
    },
    sortParticles : function(a, b){
        a = a.getEntity();
        b = b.getEntity();
        if (a.attr('bg') > b.attr('bg'))
            return 1;
        else if (a.attr('bg') < b.attr('bg'))
            return -1;
        else
            return 0;
    },
    nextTime : function(){
    	var model = this,
            particles = viewFunction.get('particles'),
            r = Crafty.viewport.width / (b-a) * neighbourR,
            entity = model.getEntity(),
            copyParticles = particles,
            size = _.size(copyParticles);
        
        copyParticles.sort(model.sortParticles);
        var bgLocal = copyParticles[0].getEntity().attr('bg');
        amountBg = 0;
        _.each(copyParticles, function(element,key){
            if (element.getEntity().attr('bg') == bgLocal){
                amountBg++;
                if (amountBg >= (size*neighbourPercent/100)) {
                    move = false;
                }
            } else {
                amountBg=1;
                bgLocal = element.getEntity().attr('bg');
            }
        });

        if (move){
    		model.calculateVelocityVector();
        	model.calculatePosition();

            _.each(particles, function(element, key){
                var distance = Crafty.math.distance(entity.x, entity.y, element.x, element.y);
                
                if (distance < neighbourR){
                    element.attr('bg', entity.attr('bg') );
                }  

            })
        }

    },
    calculateVelocityVector : function(){
    	var model = this,
    		v = model.get('v'),
    		bg = model.getEntity().attr('bg'),
    		x = model.getEntity().attr('xReal'),
    		r1 = Math.random(),
    		r2 = Math.random(),
    		r3 = Math.random();
            
    	v = c1*r1*v + c2*r2*(bi - x)+ c3*r3*(bg - x);		
    
    	model.set({'v' : v})
    },
    calculatePosition : function(){
    	var model = this,
            entity = model.getEntity(),
    		v = model.get('v'),
            x = entity.attr('xReal') + v;
            
        if (x < a) {
            x = a;
        } else if (x > b){
            x = b;
        }

		entity.attr('xReal', x);
        var y = geneticAlgorithm.calcEvaluationFunctionFromReal(x),
            pos = viewFunction.getCraftyPositionFor(x, y);

        entity.x = pos.x - entity.w/2;
        entity.y = pos.y - entity.h/2;
    },
    checkAndCalculateBFor: function(entity){
    	var model = this,
            bg = entity.attr('bg'),
            fx = entity.attr('fx'),
            x = entity.attr('x'),
            fbg = geneticAlgorithm.calcEvaluationFunctionFromReal(bg),
            fbi = geneticAlgorithm.calcEvaluationFunctionFromReal(bi);
    		
		if (fx > fbi){
    		bi = x;
    	}    	
        if (fx > fbg){
            entity.attr('bg', x);
            model.getEntity().attr('bg', x);
        }    
    },
});