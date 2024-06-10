<div class="tpproduct-tickets">
	<span class="searchTitle">Change Details</span>
	<div class="searchControls">
		<label for="{{dateField}}">Ticket Date</label>
		<input type="text" id="{{dateField}}" />

		<label for="{{adultSelect}}">Adults</label>
		<select id="{{adultSelect}}">
			{{#forLoop 0 11 1}}
				<option value='{{index}}A'>{{index}}</option>
			{{/forLoop}}
		</select>

		<label for="{{childSelect}}">Children</label>
		<select id="{{childSelect}}">
			{{#forLoop 0 11 1}}
				<option value='{{index}}C'>{{index}}</option>
			{{/forLoop}}
		</select>

		<button type="button" id="{{checkButton}}" class="refresh">Check</button>
	</div>
</div>

<script>
$("#{{dateField}}")
	.datepicker({
		autoclose:true,
		format: 'D d M yyyy',
		startDate: new Date("{{date}}"),
		orientation:'bottom left'
	})
	.datepicker("update", new Date("{{date}}"));
</script>