
Vue.component('tp-product-preview', {
    props: ['productId', 'serviceDate', 'detailPageUrl', 'searchConfigs'],
    data: function() {
        return {
            product: null,
            productUrl: null,
            descriptionMax: 255,
            visitMax: 75,
        };
    },
    methods: {
        loadProduct: function() {
            var params = {
                'date': this.serviceDate,
                'scu': 1,
                'qty': '1A',
                'productid': this.productId,
                'esm': 'E',
            };
            this.productUrl = this.detailPageUrl + '/' + '?' + $.param( params );
            REI.Product( this.productId, params, this.readProduct);
        },
        readProduct: function( rpl ) {
            if ( rpl != null && rpl.products != null &&
                rpl.products.length > 0 && rpl.supplier != null )
                this.product = rpl.products[0];
        },
    },
    mounted: function() {
        this.loadProduct();
    },
    computed: {
        totalPrice: function() {
            var priceObj = new TourplanAvailability( this.helper.lowestPricedAvailability( this.product ) );
            return priceObj.pricePerSCU;
        },
    },
    template: '\
        <!-- tp-product-preview --> \
        <div class="special__item"> \
            <span v-if="product != null"> \
                <slot :tp-product="product" :tp-product-url="productUrl" :tp-search-configs="searchConfigs" :tp-total-price="totalPrice"></slot>\
            </span> \
            <span v-else style="min-height: 200px"> \
                <img :src="helper.loadingImage()" class="img-responsive center-block" style="width:15%; margin-top: 30px; margin-bottom: 30px;"/> \
            </span> \
        </div> \
    '
});