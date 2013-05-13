var GVersionControl = new Class({
	initialize: function(){
		var a = $$('.radio-a');
		var b = $$('.radio-b');
		a.each(function(radio, index){
			radio.addEvent('click', function(index){
				this.show('.radio-b', index , 'forwards');
				//this.show('.radio-a', index, 'backwards');
				this.hide('.radio-b', index +1, 'backwards');
			}.bind(this, index))
		}.bind(this));
		b.each(function(radio, index){
			radio.addEvent('click', function(index){
				this.show('.radio-a', index, 'backwards');
				this.hide('.radio-a', index, 'forwards');
				//this.hide('.radio-b', index, 'backwards');
			}.bind(this, index))
		}.bind(this));
		a[0].set('checked', 'checked');
		b[1].set('checked', 'checked');
		this.hide('.radio-a', 1, 'forwards');
		this.hide('.radio-b', 1, 'backwards');
	},
	hide: function(classname, index, direction){
		var items = $$(classname);
		if(direction == 'forwards'){
			var n = items.length;
			var i = index;
		}else{
			var n = index;
			var i = 0;
		}
		for(var i = i, n = n; i<n; i++){
			var item = items[i];
			item.setStyle('visibility', 'hidden');
		}
	},
	show: function(classname, index, direction){
		var items = $$(classname);
		if(direction == 'forwards'){
			var n = items.length;
			var i = index;
		}else{
			var n = index;
			var i = 0;
		}
		for(var i = i, n = n; i<n; i++){
			var item = items[i];
			item.setStyle('visibility', '');
		}
	}
});
window.addEvent('domready', function(){
	new GVersionControl();
});