{{#if availability}}
<div class="productDetail">
	<div class="productDetailHeader">
		<span class="productName">{{name}}</span>
		<div class="adultSection">
			<span class="adultsLabel">{{serviceButtonConfig "Tickets" "adultCountLabel"}}</span>
			<span class="qtyAdults">{{getPaxCount.adults}}</span>
		</div>
		<div class="childSection">
			<span class="childrenLabel">{{serviceButtonConfig "Tickets" "childCountLabel"}}</span>
			<span class="qtyChildren">{{getPaxCount.children}}</span>
		</div>
		<div class="priceSection">
			<span class="priceLabel">Total Price</span>
			<span class="totalPrice">{{serviceButtonConfig "Tickets" "productPricePrefix"}}{{displayPrice availability.[0].AgentPrice 2}}</span>
		</div>
		<button type="button" class="book">Book</button>
	</div>
</div>
{{else}}
<div class="noAvail">
	<span>{{serviceButtonConfig "Tickets" "notFoundLabel"}}</span>
</div>
{{/if}}