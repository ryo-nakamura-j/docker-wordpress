
Vue.component('day-tours-detail-panel', {
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
  methods: {
      visitString: function() {
        return this.helper.visitString( this.r.product.amenities, 'TVI' );
      },
  },
  mounted: function() {
      if ( typeof onVueSearchResultMounted === "function" )
        onVueSearchResultMounted();
  },
  template: '\
      <!-- day-tours-detail-panel --> \
      <div class="search__item clearfix"> \
        <div class="item__inner"> \
          <div class="item__slider col-xs-12"> \
            <tp-slides :tp-id="\'carousel-\' + r.product.code" :tp-src-list="helper.getProductImageSrcList(r.product, imageMax)"/> \
          </div> \
          <div class="price__sp visible-xs visible-sm"> \
            <p class="price__middle">{{helper.getSearchLabel(searchConfigs, "srb", "searchPricePrefix")}} <span>{{\'$\' + helper.displayPrice( r.availability.TotalPrice, 2 )}}</span> <br> {{helper.getSearchLabel(searchConfigs, "srb", "searchPriceSuffix")}}</p> \
          </div> \
        </div> \
        <div class="item__content"> \
           <p class="p__txt01">{{r.product.name}}</p> \
           <p class="p__txt02"> \
             <span class="text01" v-if="helper.amenities( r.product.amenities, \'TDU\').length > 0">\
                <span v-for="a in helper.amenities( r.product.amenities, \'TDU\')"> \
                  {{a.value}} \
                </span> \
             </span> \
             <span class="text02"> \
                {{r.product.dst}} \
             </span> \
           </p> \
           <p class="p__txt03">Visit \
              {{ helper.convertContentMaxLimit( visitString(), visitMax ) }} \
           </p> \
           <p class="p__txt04">{{helper.convertContentMaxLimit( helper.getNotes( r.product.notes, \'SDO\', \'text\' ), descriptionMax )}}</p> \
           <a :href="helper.productURL( r.destinationUrl, r.product)">{{dataListLabel("see_detail", "See detail")}}</a> \
           <p class="p__car">\
              <span v-for="a in helper.amenities( r.product.amenities, \'TIC\')"> \
                <img :src="helper.getTourIconSrc(a.key)" :alt="a.value" :title="a.value" /> \
              </span> \
           </p> \
        </div> \
        <div class="item__price hidden-xs hidden-sm"> \
          <p class="price__middle">{{helper.getSearchLabel(searchConfigs, "srb", "searchPricePrefix")}} <span>{{\'$\' + helper.displayPrice( r.availability.TotalPrice, 2 )}}</span> <br> {{helper.getSearchLabel(searchConfigs, "srb", "searchPriceSuffix")}}</p> \
        </div> \
     </div> \
  '
});