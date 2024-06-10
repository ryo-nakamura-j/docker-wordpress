
Vue.component('accom-detail-panel', {
  props: ['tpResult', 'tpIndex', 'searchConfigs', 'tpClass'],
  data: function() {
      return {
        descriptionMax: 255,
        visitMax: 75,
        imageMax: 3,
      };
  },
  computed: {
      r: { get: function() { return this.tpResult; } }
  },
  methods: {},
  mounted: function() {
      if ( typeof onVueSearchResultMounted === "function" )
        onVueSearchResultMounted();
  },
  template: '\
    <!-- accom-detail-panel --> \
    <div class="search__item clearfix"> \
        <div class="item__inner"> \
            <div class="item__slider col-xs-12"> \
                <tp-slides :tp-id="\'carousel-\' + r.product.code" :tp-src-list="helper.getSupplierImageSrcList( r.supplier, imageMax )"/> \
            </div> \
            <div class="price__sp visible-xs visible-sm"> \
                <p class="price__middle"> \
                    {{helper.getSearchLabel(searchConfigs, "srb", "searchPricePrefix")}} <span> <br> {{\'$\' + helper.displayPrice( r.availability.pricePerSCU, 2)}}</span> <br> {{helper.getSearchLabel(searchConfigs, "srb", "searchPriceSuffix")}} \
                </p> \
            </div> \
        </div> \
        <div class="item__content"> \
            <p class="p__title">{{r.supplier.name}}</p> \
            <p class="p__address">{{helper.convertContentMaxLimit( helper.getNotes( r.supplier.notes, "TST", "text"), visitMax )}}</p> \
            <p class="p__room">{{r.product.name}}</p> \
            <p class="p__content">{{helper.convertContentMaxLimit( helper.getNotes( r.supplier.notes, "SDS", "text"), descriptionMax )}}</p> \
            <a :href="helper.supplierURL( r.destinationUrl, r.supplier, r.product )">{{dataListLabel("see_detail", "See detail")}}</a> \
        </div> \
        <div class="item__price hidden-xs hidden-sm"> \
            <p class="price__middle"> \
                {{helper.getSearchLabel(searchConfigs, "srb", "searchPricePrefix")}} <span> {{\'$\' + helper.displayPrice( r.availability.pricePerSCU, 2)}}</span> {{helper.getSearchLabel(searchConfigs, "srb", "searchPriceSuffix")}} \
            </p> \
        </div> \
    </div> \
  '
});