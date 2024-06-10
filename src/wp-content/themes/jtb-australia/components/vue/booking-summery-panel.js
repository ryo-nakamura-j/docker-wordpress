
Vue.component('booking-summery-panel', {
  props: ['searchConfigs', 'deliveryFeeAmount', 'paymentfeePrice', 'subTotal', 'serviceLineList', 'classContainer'],
    data: function() {
        return {}
    },
    computed: {
        itemsLabel: function() { return this.dataListLabel("items", "items"); }
    },
    template: ' \
    <div :class="classContainer"> \
        <div class="summery_heading"> \
            <h4>{{dataListLabel("your_order", "YOUR ORDER")}} ({{serviceLineList.length + \' \' + itemsLabel }})</h4> \
        </div> \
        <div class="col-xs-12 summery_content"> \
            <div v-for="sl in serviceLineList">  \
                <div class="col-md-12 col-xs-12 product-title"> \
                    {{helper.propWithLang( sl, "productname" )}} \
                </div> \
                <div class="col-md-12 col-xs-12"> \
                    <span class="pull-right">{{helper.serviceButtonConfig("", "cartPricePrefix") + (sl.price / 100).toFixed(2)}}</span> \
                </div> \
                <div v-for="cfg in sl.configs" class="col-xs-12"> \
                    <div v-if="cfg.adults > 0"> \
                        <div class="col-xs-12"> \
                            {{cfg.adults + \' \' + helper.serviceButtonConfig("", "adultCountLabel")}} \
                        </div> \
                        <div v-for="px in cfg.pax" class="col-xs-12"> \
                            <div class="col-xs-12" v-if="px.paxtype==\'A\'"> \
                                {{helper.getNameDesc( px, "paxNameFormatLang" )}} \
                            </div> \
                        </div> \
                    </div> \
                    <div v-if="cfg.children > 0"> \
                        <div class="col-xs-12"> \
                            {{cfg.children + \' \' + helper.serviceButtonConfig("", "childCountLabel")}} \
                        </div> \
                        <div v-for="px in cfg.pax" class="col-xs-12"> \
                            <div class="col-xs-12" v-if="px.paxtype==\'C\'"> \
                                {{helper.getNameDesc( px, "paxNameFormatLang" )}} \
                            </div> \
                        </div> \
                    </div> \
                    <div v-if="cfg.infants > 0"> \
                        <div class="col-xs-12"> \
                            {{cfg.infants + \' \' +  + helper.serviceButtonConfig("", "infantCountLabel")}} \
                        </div> \
                        <div v-for="px in cfg.pax" class="col-xs-12"> \
                            <div class="col-xs-12" v-if="px.paxtype==\'I\'"> \
                                {{helper.getNameDesc( px, "paxNameFormatLang" )}} \
                            </div> \
                        </div> \
                    </div> \
                </div> \
            </div> \
        </div> \
        <div class="col-xs-12 summery_content"> \
            <div class="col-xs-8 col-md-8">  \
                <span class="sub_label">{{dataListLabel("sub_total", "SUB TOTAL")}}</span> \
            </div> \
            <div class="col-xs-4 col-md-4"> \
                <span class="sub_label pull-right"> \
                    {{helper.serviceButtonConfig("", "cartPricePrefix") + (subTotal / 100).toFixed(2)}} \
                </span> \
            </div> \
            <div v-if="deliveryFeeAmount" class="col-xs-8 col-md-8">{{dataListLabel("delivery_fee", "Delivery Fee")}}</div> \
            <div v-if="deliveryFeeAmount" class="col-xs-4 col-md-4"> \
                <span class="pull-right"> \
                    {{helper.serviceButtonConfig("", "cartPricePrefix") + (deliveryFeeAmount / 100).toFixed(2)}} \
                </span> \
            </div> \
            <div id="paymentfeeLabel" hidden></div> \
            <div class="col-xs-8 col-md-8" v-show="paymentfeePrice > 0">{{dataListLabel("payment_fee", "Payment Fee")}}</div> \
            <div class="col-xs-4 col-md-4" v-show="paymentfeePrice > 0"> \
                <span class="pull-right" id="paymentfeePrice"> \
                    {{helper.serviceButtonConfig("", "cartPricePrefix") + (paymentfeePrice / 100).toFixed(2)}} \
                </span> \
            </div> \
        </div> \
        <div class="col-xs-12 summery_content"> \
            <div class="col-xs-8 col-md-8">  \
                <span class="sub_label">{{dataListLabel("order_total", "ORDER TOTAL")}}</span> \
            </div> \
            <div class="col-xs-4 col-md-4">  \
                <span class="totalPrice pull-right sub_label"></span> \
            </div> \
        </div> \
    </div> \
    '
});