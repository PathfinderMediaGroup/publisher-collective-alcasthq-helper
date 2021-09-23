<script>
(function(w,d){
	function placeholder (obj) {
		// Create our new element
		var newDiv = d.createElement('div');
		newDiv.id = obj.id;

		if (obj.style) newDiv.style = obj.style;
		if (obj.append) d.body.appendChild(newDiv);
		if (obj.before) obj.before.parentNode.insertBefore(newDiv, obj.before);
		if (obj.after) obj.after.parentNode.insertBefore(newDiv, obj.after.nextSibling);

		obj.el = newDiv;
		return obj; // Return the same obj, but with the new element attached
	}
	w.addEventListener('DOMContentLoaded', function(){
		placeholder({
			id: 'nn_mobile_mpu1',
			after: d.querySelector('div.post-content > *:nth-child(2)')
		});
		placeholder({
			id: 'nn_mobile_mpu2',
			before: d.querySelector('div.post-content > *:nth-child(10)')
		});		
		placeholder({
			id: 'nn_player',
			style: 'text-align:center; display:block; width:100%; clear:both; padding:15px 0;',
			before: d.querySelector('div.post-content > *:nth-child(6)')
		});
	});
})(window, document);
</script>