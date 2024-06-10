<section id="tourSearch">
	<h3>Day Tour Search</h3>
	<div class="row">
		<div class="col-xs-5">
			<label for="{{departingSelect}}" class="control-label">Depart From</label>
		</div>
		<div class="col-xs-7 input-column">
			<select class="form-control" id="{{departingSelect}}"></select>
		</div>
	</div>	

	<div class="row">
		<div class="col-xs-5">
			<label for="{{destinationsSelect}}">Destination</label>
		</div>
		<div class="col-xs-7 input-column">
			<select id="{{destinationsSelect}}" class="form-control" multiple="multiple"></select>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-5">
			<label for="{{visitsSelect}}">Visits</label>
		</div>
		<div class="col-xs-7 input-column">
			<select id="{{visitsSelect}}" class="form-control" multiple="multiple"></select>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-5">
			<label for="{{dateField}}">Search Date</label>
		</div>
		<div class="col-xs-7 input-column">
			<input id="{{dateField}}" class="form-control" type="date" />
		</div>
	</div>

	<div class="row">
		<div class="col-xs-5">
			<label for="{{keywordField}}">Keyword</label>
		</div>
		<div class="col-xs-7 input-column">
			<input id="{{keywordField}}" class="form-control" type="text" />
		</div>
	</div>

	<div class="row">
		<div class="col-xs-5">
			<label for="{{durationsSelect}}">Duration</label>
		</div>
		<div class="col-xs-7 input-column">
			<select id="{{durationsSelect}}" class="form-control" multiple="multiple"></select>
		</div>
	</div>

	{{!-- <div class="row">
		<div class="col-xs-6">
			<label for="{{durationsField}}">Duration</label>
		</div>
		<div class="col-xs-6 input-column">
			<div class="dropdown">
				<button id="{{durationsField}}" class="btn btn-default" type="button" data-toggle="dropdown">
					None Selected <span class="caret"></span>
				</button>
				<ul id="durationsPanel" class="dropdown-menu dropdown-menu-form" style="width:800px;">
					<li>
						<ul id="durationsList"></ul>
					</li>
					<li>
						<input id="{{durationsOKButton}}" type="button" value="OK" />
					</li>
				</ul>
			</div>
		</div>
	</div> --}}

	<div class="row">
		<div class="col-xs-5">
			<label for="{{themesSelect}}">Themes</label>
		</div>
		<div class="col-xs-7 input-column">
			<select id="{{themesSelect}}" class="form-control" multiple="multiple"></select>
		</div>
	</div>

	{{!-- <div class="row">
		<div class="col-xs-6">
			<label for="{{themesField}}">Themes</label>
		</div>
		<div class="col-xs-6 input-column">
			<div class="dropdown">
				<button id="{{themesField}}" class="btn btn-default" type="button" data-toggle="dropdown">
					None Selected <span class="caret"></span>
				</button>
				<ul id="themesPanel" class="dropdown-menu dropdown-menu-form" style="width:800px;">
					<li>
						<ul id="themesList"></ul>
					</li>
					<li>
						<input id="{{themesOKButton}}" type="button" value="OK" />
					</li>
				</ul>
			</div>
		</div>
	</div> --}}
	{{!-- <div class="row">
		<div class="col-xs-12">
			<input type="button" id="debugButton" value="DEBUG" />
		</div>
	</div> --}}

	<div class="row searchButtonRow">
		<div class="col-xs-12">
			<input id="{{searchSubmitButton}}" type="button" value="Search"/>
		</div>
	</div>
</section>