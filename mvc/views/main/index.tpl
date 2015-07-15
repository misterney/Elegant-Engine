	
{* + ../header.tpl *}

<div id="container"> 
		<div class="ui sticky">
			<div class="row">
				<div class="one wide column"></div>
				<div class="fourteen wide column">
					<div class="ui teal pointing  menu" id="menu"> 
						<a class="header item" href="/" style="font-size: 18px;">{*html.title*}</a>
						<a class="active item" href="/" style="font-size: 16px;">  Главная</a> 
						<a class="item" href="/" style="font-size: 16px;">  Магазин</a> 
						<a class="item" href="/" style="font-size: 16px;">  Привелегии</a> 
						<a class="item" href="/" style="font-size: 16px;">  Контакты</a> 
	    				</div>
				</div>
				<div class="one wide column"></div>
       		</div>
		</div>




{* + online.tpl *}

<div id="block_3">

<br><br><br><br><br><br><br>
<div class="ui grid">
	{%*html.items*}
	{?*html.items:improve=1*}
		<div class="row">
			<div class="three wide column"></div>
			<div class="ten wide column">
				<div class="ui teal tall stacked segment">
					<div class="ui accordion">
						<div class="title">
							<center><p style="font-size: 25px;">Привелегия: {*html.items:name*}</p></center>
						</div>
						<div class="content">
							<center>
								{%*html.tablePrepayment*}
									{?*html.tablePrepayment:^KEY=html.items:^KEY*}
									{?!*html.tablePrepayment:prepayment="false"*}
									<div class="ui inverted divider"></div>
									<table class="ui collapsing basic striped table">
										<thead>
											<tr>
												<th>Имеющиеся привелегия</th>
												<th>Сумма доплаты</th>
											</tr>
										</thead>
										
										{*html.tablePrepayment:prepayment*} 
												
										
									</table>
									{*html.tablePrepayment:prepayment="false"*?!}
									{*html.tablePrepayment:^KEY=html.items:^KEY*?}
										
								{*html.tablePrepayment*%}
							<div class="ui inverted divider"></div>
							<p style="font-size: 21px;">Описание</p>
							<div class="ui inverted divider"></div>

							</center>
							{*html.items:desc*}
							<div class="ui inverted divider"></div>
							<center>
								<div class="positive huge ui button">Купить за {*html.items:price*} <i class="ruble icon"></i></div>
							</center>
						</div>
					</div>
				</div>
			</div>
			<div class="three wide column"></div>
		</div>
	{*html.items:improve=1*?}
	{*html.items*%}
</div>

<br><br><br><br><br><br><br>

</div>
<div id="block_1" style="height: 100vh;  vertical-align: middle; text-align: center;">
<center>
	<div class="ui focus  form" style="padding: 120px 140px 0px 140px">
		<label style="font-size: 55px">Донат</label><br><br>
		<label style="font-size: 22px">Просто введите свой ник и выберите услугу</label>
		<br><br>
		<div class="ui grid">
		<div class="three wide column"></div>
		<div class="ten wide column">
		<div class="field">
			<div class="ui input focus">
  				<input type="text" placeholder="Ваш никнейм">
			</div>
		</div>
<div class="ui fluid selection dropdown">
    <span class="text">Выберите привелегию</span>




    <div class="menu">

      {?!*html.itemsList.improve=0*}
		<div class="divider"></div>
		<div class="header">
			<i class="thumbs up large icon"></i>
			Привелегии
		</div>
	{%*html.itemsList.improve*}
		<div class="item" id="html.itemsList.improve:id">
			{*html.itemsList.improve:name*} (Бессрочно | <strong>{*html.itemsList.improve:price*}</strong> <i class='ruble icon'></i> )
		</div>
	{*html.itemsList.improve*%}
      {*html.itemsList.improve=0*?!}
	

      {?!*html.itemsList.item=0*}
		<div class="divider"></div>
		<div class="header">
		<i class="legal large icon"></i>
		Предметы
		</div>

	{%*html.itemsList.item*}
		<div class="item" id="html.itemsList.item:id">
			{*html.itemsList.item:name*} <strong>{*html.itemsList.item:price*}</strong> <i class='ruble icon'></i>
		</div>
	{*html.itemsList.item*%}
      {*html.itemsList.item=0*?!}
	

      {?!*html.itemsList.service=0*}
		<div class="divider"></div>
		<div class="header">
			<i class="star large icon"></i>
			Услуги
		</div>

	{%*html.itemsList.service*}
		<div class="item" id="html.itemsList.service:id">
			{*html.itemsList.service:name*} <strong>{*html.itemsList.service:price*}</strong> <i class='ruble icon'></i>
		</div>
	{*html.itemsList.service*%}
      {*html.itemsList.service=0*?!}	


    </div>
  </div>
		<br>
		<div class="field">
			<div class="ui input focus">
  				<input type="text" placeholder="Скидочный код (Необезательно)">
			</div>
		</div>
		<center>
			<br><br>
			<div class="positive  huge ui button">Купить за 100 <i class="ruble icon"></i></div>
		</center>
		</div>
		<div class="three wide column"></div>
	</div>
</center>
</div>

<div id="block_3" style="height: 100vh;">

</div>

{* + ../footer.tpl*}