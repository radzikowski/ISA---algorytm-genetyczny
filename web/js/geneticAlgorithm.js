GeneticAlgorithm = Backbone.Model.extend({
    defaults: {
    	'a' : null,
    	'b' : null,
    	'precision' : null, //ilosc miejsc po przecinku
    	'aumountOfChild' : null,
    	'amountOfChromosoms' : null
    },
    initialize: function(){
    	
    	var model = this;
    	var a = model.get('a');
    	var b = model.get('b');
    	var precision = model.get('precision');
    	var amountOfChild = parseInt(((b-a)/Math.pow(10,-1*precision)) + 1);
    	var amountOfChromosoms = parseInt(Math.ceil(Math.log(amountOfChild)/Math.log(2)));
    	
    	model.set({'amountOfChild' : amountOfChild, 'amountOfChromosoms': amountOfChromosoms});
    },
    getAmountOfChild : function() {
    	return this.get('amountOfChild');
    },
    getA : function(){
    	return this.get('a');
    },
    getB : function(){
    	return this.get('b');
    },
    getPrecision : function(){
    	return this.get('precision');
    },
    randomXReal : function(){
    	var model = this;
    	var a = this.get('a');
    	var prec = Math.pow(10, parseInt(model.getPrecision()));
    	var amount = parseInt(model.getAmountOfChild()); 

    	//amountOfChild +1 becouse random get from 0 to almost 1
    	var x = Math.random()*(amount + 1);
    	var x1 = Math.floor(x);
    	return (Math.floor(x1 + parseInt(a * prec)))/ prec;
    },
    calcXIntFromReal : function(xReal){
    	var model = this;
    	var a = model.get('a');
    	var b = model.get('b');
    	var l = model.get('amountOfChromosoms');

    	var xInt = parseInt((1/(b-a))* (xReal - a) * Math.pow(2, l) - 1);
    	return xInt;
    },
    calcXRealFromInt : function(xInt){
    	var model = this;
    	var a = model.get('a');
    	var b = model.get('b');
    	var l = model.get('amountOfChromosoms');
    	var prec = Math.pow(10, parseInt(model.get('precision')));
    	var xReal = (b-a)*xInt / (Math.pow(2, l) - 1) + a
    	var x1 = parseInt(Math.round(xReal * prec));
    	var x2 = x1 / prec;
    	return x2;  
    },
    calcXBinFromInt : function(xInt){
    	var model = this;
    	var xBin = '';
    	var l = model.get('amountOfChromosoms');
    	for (var i=0; i<l ;i++){
    		xBin = ((parseInt(xInt%2) > 0) ? '1' : '0') + xBin;
    		xInt = parseInt(xInt/2);
    	}
    	return xBin;
    },
    calcEvaluationFunctionFromReal : function(xReal){
    	var model = this;
//    	F(x)= (x MOD 1) * (COS( 20 * π * x) – SIN(x))
    	var valueFunc = (xReal%1) * Math.cos(20 * Math.PI *xReal) - Math.sin(xReal);
    	// get 1 point more of precision to show correct result of function
    	var prec = Math.pow(10, parseInt(model.getPrecision())+1);
    	var x = Math.round(valueFunc * prec);
    	return x / prec; 
    }
});
