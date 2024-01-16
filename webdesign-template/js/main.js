document.addEventListener('DOMContentLoaded', function(){
	// für Textfelder im modernen Stil
	document.querySelectorAll('fieldset .form-row input, fieldset .form-row textarea, fieldset .form-row select').forEach((input) => {
		input.addEventListener('change', function(){
			var length = this.value.length;
			if(length) {
				this.parentNode.classList.add('populated');
			} else {
				this.parentNode.classList.remove('populated');
			}
		});
		input.addEventListener('focus', function(){
			this.parentNode.classList.add('focus');
		});
		input.addEventListener('blur', function(){
			this.parentNode.classList.remove('focus');
		});
		if(input.value.length) {
			input.parentNode.classList.add('populated');
		}
	});

	// "Zeige Passwort"-Funktion
	document.querySelectorAll('.form-row.password').forEach((formRow) => {
		let btnsShowPassword = formRow.getElementsByClassName('showpassword');
		let txtsPassword = formRow.querySelectorAll('input');
		if(btnsShowPassword.length > 0 && txtsPassword.length > 0) {
			['mousedown'].forEach(function(e){
				btnsShowPassword[0].addEventListener(e, function(){
					txtsPassword[0].type = 'text';
				});
			});
			['mouseup', 'mouseleave'].forEach(function(e){
				btnsShowPassword[0].addEventListener(e, function(){
					if(txtsPassword[0].type == 'text') {
						txtsPassword[0].type = 'password';
						txtsPassword[0].focus();
					}
				});
			});
		}
	});
});
