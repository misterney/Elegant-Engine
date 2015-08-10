function event(typeOfEvent){
	switch(typeOfEvent){
		case buy:
			var login = $("#login").val();
			var discount = $("#discount").val();
			var privilege = $("#privelege :selected").val();
			if(login === ""){
				alert('Введите ник!');
			} else {
				alert('Происходит покупка');
			}
			[break]
		case default:
			console.log('Ошибка, неизвестный ивент');
			[break]
	}
}


$.ajax({
	type : "GET",
	dataType : "HTML",
	url : "update2/EventsHandler.php?event=lastBuy",
	success : function (response) {
		$("#lastBuy").html(response);
	}
});

function check(){
	var login = $("#login").val();
	var discount = $("#discount").val();
	var privilege = $("#privelege :selected").val();
	if (login === "") {
		alert('Введите ник!');
	} else {
		if (discount === "") {
			document.location.href='update2/EventsHandler.php?event=redirect&login='+login+'&item='+privilege;
		} else {
			document.location.href='update2/EventsHandler.php?event=redirect&login='+login+'&item='+privilege+'&discount='+discount;
		}
	}
}

function checking(){
	var login = $("#login_check").val();
	if (login === "") {
		alert('Введите ник!');
	} else {
		$.ajax({
			type : "GET",
		    	dataType : "HTML",
		    	url : "update2/EventsHandler.php?event=checkUser&login="+login,
		    	success : function (response) {
		 		$("#check").html(response);
		    	}
		});
	}
}

setInterval(function online(){
	$.ajax({
		type : "GET",
		dataType : "HTML",
		url : "update2/EventsHandler.php?event=getOnline",
		success : function (response) {
			$("#online").html(response);
		}
	});
},1000);

$.ajax({
	type : "GET",
	dataType : "HTML",
	url : "update2/EventsHandler.php?event=getListItems",
	success : function (response) {
		$("#items").html(response);
	}
});
			
$.ajax({
	type : "GET",
	dataType : "HTML",
	url : "update2/EventsHandler.php?event=itemsForPurchase",
	success : function (response) {
		$("#itemsFP").html(response);
	}
});

function summ(){
	var login = $("#login").val();
	var privelege = $("#privelege :selected").val();
	var discount = $("#discount").val();
	if(discount){
		if(login){
			$.ajax({
		    		type : "GET",
				dataType : "HTML",
				url : "update2/EventsHandler.php?event=getSumm&login="+login+"&item="+privelege+"&discount="+discount,
				success : function (response) {
					$("#summ").html(response);
		    		}
			});
		} else {
			$.ajax({
		    		type : "GET",
		    		dataType : "HTML",
		    		url : "update2/EventsHandler.php?event=getSumm&item="+privelege+"&discount="+discount,
		    		success : function (response) {
		  			$("#summ").html(response);
		    		}
			});
		}
	} else {
		if(login){
			$.ajax({
		    		type : "GET",
		    		dataType : "HTML",
		    		url : "update2/EventsHandler.php?event=getSumm&login="+login+"&item="+privelege,
		    		success : function (response) {
					$("#summ").html(response);
		    		}
			});
		} else {
			$.ajax({
		    		type : "GET",
		    		dataType : "HTML",
		    		url : "update2/EventsHandler.php?event=getSumm&item="+privelege,
		    		success : function (response) {
					$("#summ").html(response);
		    		}
			});
		}
	}
}