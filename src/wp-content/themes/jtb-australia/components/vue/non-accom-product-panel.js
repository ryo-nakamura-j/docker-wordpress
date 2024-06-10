
Vue.component('non-accom-product-panel', {
    props: {
        originalProduct: { default: null },
        searchConfigs: { default: null },
    },
    data: function() {
        return {
            isLoading: true,
            product: this.originalProduct,
        };
    },
    computed: {
        el: function() {
            vm = this;
            return $( vm.$refs.el );
        },
        adultContainerClass: function() {
            return 'price adult ' 
            + ( this.product.adult_rate_only ? 'col-xs-8 col-xs-offset-2 ' : 'col-xs-6 ' )
            + ( this.product.wpRates && this.product.wpRates.adult ? 'multipleRates' : '' );
        },
        adultArrowClass: function() {
            return 'fa fa-caret-down down-arrow '
            + ( this.product.adult_rate_only ? 'pull-left center ' : 'pull-right left ');
        },
    },
    mounted: function() {
        var vm = this;
        var productImpressions = [];
        vm.getRates(function(supplier, rates) {
            var ind = 1;
            _.forEach(rates, function(rate) {
                var product = new TourplanProduct(
                    _.find(supplier.products, function(prod) {
                        return rate.productid == prod.productid; 
                    }
                ));
                vm.product = _.extend( vm.product, product, {rates: rate} );
                vm.isLoading = false;

                productImpressions.push({
                    'id' : vm.product.productid,
                    'name' : vm.product.name,
                    'category' : vm.product.srb,
                    'list' : 'Japan Rail Pass',
                    'price' : {
                        'adult' :(vm.product.rates.adult/100),
                        'child':(vm.product.rates.child/100)
                    },
                    'position' : ind
                });
                ind++;

                Vue.nextTick(function() {
                    vm.el.find('select.passenger-counter').change(function() {
                        var counters = _.sum(_.map(vm.el.find('select.passenger-counter'), function(x) { return parseInt($(x).val()); }));
                        if (counters > 0) {
                            vm.el.find('button.book').removeProp("disabled");
                        } else {
                            vm.el.find('button.book').prop("disabled", "disabled");
                        }
                    }).change();

                    vm.el.find('button.book').click(function() {

                        var config = TourplanRetailUtilities.ParseQty(vm.getProductQty());
                        var pax = [];
                        var price = 0;

                        for (i = 0; i < config.adults; i++) { pax.push({paxtype:'A'}); price += rate.adult; }
                        for (i = 0; i < config.children; i++) { pax.push({paxtype:'C'}); price += rate.child; }
                        for (i = 0; i < config.infants; i++) { pax.push({paxtype:'I'}); price += rate.infant; }

                        config['pax'] = pax;

                        CartInterface.addServiceLine(
                            supplier.supplier,
                            vm.product,
                            vm.product.availability[0],
                            { 
                                rateid: 'Default',
                                qty: vm.getProductQty(),
                                configs:[config],
                                price:price,
                                pricedisplay: (price / 100).toFixed(2)
                            },
                            {
                                success: function() { window.location = $("#tourplanRetailConfig").attr("itinerarypage"); }
                            }
                        );
                    });
                });
            })

            dataLayer.push({
                'event':'gtm.dom',
                'eventCategory':'Ecommerce', 
                'eventAction': 'Impression',
                'currencyCode': $("#tourplanRetailConfig").attr('currency'),
                'ecommerce': {
                    'impressions': productImpressions
                }
            })
        });
    },
    methods: {
        getSearchDate: function() {
            var vm = this;
            return (_.isEmpty(this.searchConfigs.searchDate) ?
                moment().add(vm.helper.getServiceButtonConfig(vm.searchConfigs.srb, "searchDateOffset", 0), "days").format('YYYY-MM-DD') :
                moment(vm.searchConfigs.searchDate, 'YYYY-MM-DD').format('YYYY-MM-DD')
                );
        },
        getQty: function() {
            var vm = this;
            return (_.isEmpty(vm.searchConfigs.defaultQty) ?
                vm.helper.getServiceButtonConfig(vm.searchConfigs.srb, "defaultQty") :
                vm.searchConfigs.defaultQty);
        },
        getProductQty: function() {
            var adultQty = $(this.el).find('select[name=adults]').val() || "0A";
            var childQty = $(this.el).find('select[name=children]').val() || "0C";
            var infantQty = $(this.el).find('select[name=infants]').val() || "0I";
            qty = adultQty + childQty + infantQty;

            return qty;
        },
        getRates: function( callback ) {
            var vm = this;
            REI.Supplier_Old(vm.searchConfigs.supplierid, {
                date:vm.getSearchDate(),
                scu:1,
                qty:vm.getQty(),
                info:'roomTypes'
            }).done(function(supplier) {
                REI.Rates({
                    ids:vm.product.productid,
                    date:vm.getSearchDate()
                }).done(function(data) {
                    callback(supplier, data);
                });
            })
        },
    },
    template: ' \
    <div ref="el"> \
        <div v-if="isLoading" class="resultControl"> \
            <div style=\'width:100%;text-align:center\'> \
                <img :src="helper.loadingImage()" /> \
            </div> \
        </div> \
        <div v-if="!isLoading" class="row"> \
            <div :class="adultContainerClass"> \
                <div class="rail_price_inner"> \
                    <p class="age">{{helper.getServiceButtonConfig(searchConfigs.srb, "adultCountLabel", "Adult")}} {{helper.ageRange( product.info.Option.OptGeneral.Adult_From, product.info.Option.OptGeneral.Adult_To )}}</p> \
                    <p class="amount"><span class="symbol">{{helper.getServiceButtonConfig( searchConfigs.srb, "productPricePrefix" )}}</span><br /> \
                    {{product.rates.adult > 0 ? helper.displayPrice( product.rates.adult, 0 ) : helper.getServiceButtonConfig(searchConfigs.srb, "rateNotAvailLabel", "N/A")}} \
                    </p> \
                    <p v-if="product.configs.JRAdultRate" class="wp-amount">{{product.configs.JRCurrency}}<br />{{product.configs.JRAdultRate}}</p> \
                    <select class="passenger-counter" name="adults" :disabled="product.rates.adult <= 0"> \
                        <option v-for="n in parseInt(product.dropdown_max)" :value="(n-1) + \'A\'">{{n-1}}</option> \
                    </select> \
                    </div> \
                <i :class="adultArrowClass"></i> \
            </div> \
            <div v-show="!product.adult_rate_only" :class="\'price child col-xs-6 \' + ( product.wpRates && product.wpRates.child ? \'multipleRates\' : \'\' )"> \
                <div class="rail_price_inner"> \
                <p class="age">{{helper.getServiceButtonConfig(searchConfigs.srb, "childCountLabel", "Child")}} {{helper.ageRange( product.info.Option.OptGeneral.Child_From, product.info.Option.OptGeneral.Child_To)}}</p> \
                <p class="amount"><span class="symbol">{{helper.getServiceButtonConfig( searchConfigs.srb, "productPricePrefix")}}</span><br /> \
                {{product.rates.child > 0 ? helper.displayPrice( product.rates.child, 0 ) : helper.getServiceButtonConfig(searchConfigs.srb, "rateNotAvailLabel", "N/A")}} \
                </p> \
                <p v-if="product.configs.JRChildRate" class="wp-amount">{{product.configs.JRCurrency}}<br />{{product.configs.JRChildRate}}</p> \
                <select class="passenger-counter" name="children" :disabled="product.rates.child <= 0"> \
                    <option v-for="n in parseInt(product.dropdown_max)" :value="(n-1) + \'C\'">{{n-1}}</option> \
                </select> \
                </div> \
                <i class="fa fa-caret-down down-arrow right pull-left"></i> \
            </div> \
            <button type="button" class="book">{{dataListLabel( "buy_now", "Buy Now" )}}</button> \
        </div> \
    </div> \
    '
});