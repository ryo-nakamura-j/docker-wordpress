
Vue.component('product-search-panel', {
  props: {
    sectionConfig: { default: null },
    idContainer: { default: 'product-search-panel-component'},
    isDstAutoHide: { default: false },
    // Stylings
    classLabel: { default: 'visible-xs visible-sm'},
    classSearchParamSection: { default: 'box__search' },
    classSearchButton: { default: 'btnsearch' },
    classSearchParamLocation: { default: 'search__s'},
    classSearchParamAmenities: { default: 'search__amenities'},
    classSearchParamQty: { default: 'search__passengers' },
    // Label extras
    qtyPostfix: { default: 'Passengers' },
    // Turn on/off params
    showRegion: { default: false },
    showToDate: { default: false },
    showScu: { default: false },
    showAmenities: { default: true },
    // Selectors used in DOM modification
    jsSelectorAmenities: { default: 'tp_amenity_filter' },  
    jsSelectorDate: { default: 'tp_date' }, 
  },
  methods: {
    getQtyValue: function (n) { return n + 'A'; },
    getAmenityLabel: function (a) { return a.control_label; },
    getQtyDescription: function (n) { return n + " " + this.qtyPostfix},
  },
  data: function() {
    return {
      amenityList: this.sectionConfig.amenity_filters,
      scuMax: this.sectionConfig.date_config.max_scu,
      qtyMax: 10,
    };
  },
  computed: {
    dstList: function() {
      // Compute dstList here
      return this.$parent.dstList;
    },
      dstLabel: function() { 
        return this.helper.getSearchLabel( this.sectionConfig.search_config, "srb", "destinationsLabel", "Destination");
      },
      dateInLabel: function() { 
        return this.helper.getSearchLabel( this.sectionConfig.search_config, "srb", "dateInLabel", "Check In");
      },
      qtyLabel: function() { 
        return this.helper.getSearchLabel( this.sectionConfig.search_config, "srb", "qtyLabel", "Rooms");
      },
      buttonLabel: function() { 
        return this.helper.getSearchLabel( this.sectionConfig.search_config, "srb", "searchButtonLabel", "Search");
      },
      // labels
      lclLabel: function() { return this.dataListLabel("region", "Region"); },
      toDateLabel: function() { return this.helper.getSearchLabel( this.sectionConfig.search_config, "srb", "dateOutLabel", "Check Out") },
      scuLabel: function() { return this.helper.getSearchLabel( this.sectionConfig.search_config, "srb", "scuLabel", "Nights"); },
  },
  template: '\
<!-- product-search-panel-component --> \
<span :id="idContainer"> \
  <div :class="classSearchParamSection"> \
    <input type="hidden" name="srb" /> \
    <input type="hidden" name="cty" /> \
 \
    <span> \
      <label :class="classLabel">{{ dstLabel }} </label> \
      <tp-autocomplete \
          :list="dstList" \
          v-model="$parent.dstValue" \
          :classPrefix="classSearchParamLocation" \
          property="label" \
          threshold="-1"\
          :auto-hide="isDstAutoHide"\
          v-on:onchange="$parent.dstOnChange" \
          :placeholder="dstLabel" \
      /> \
    </span> \
    <span v-if="showRegion"> \
      <label :class="classLabel">{{ lclLabel }}</label> \
      <select name="lcl" :class="classSearchParamLocation" \
          :data-placeholder="lclLabel" > \
      </select> \
    </span> \
    <span v-if="!showToDate"> \
      <label :class="classLabel">{{ dateInLabel }} </label> \
      <input type="text" name="date" :class="jsSelectorDate" \
        :data-placeholder="dateInLabel"/> \
    </span> \
    <span v-if="showToDate"> \
      <label :class="classLabel" for="pick_date">{{ dateInLabel }}</label> \
      <label :class="classLabel" for="pick_date1">{{ toDateLabel }}</label> \
      <input type="text" id="pick_date" name="date" :class="\'search__date \' + jsSelectorDate" value="" placeholder="Check in"> \
      <i class="ico_arrow_right"></i> \
      <input type="text" id="pick_date1" name="toDate" :class="\'search__date \' + jsSelectorDate" value="" placeholder="Check out"> \
    </span> \
    <span v-if="showScu"> \
      <label :class="classLabel">{{ scuLabel }}</label> \
      <select name="scu" \
        :data-placeholder="scuLabel"> \
        <option :value="n" v-for="n in scuMax"> \
          {{n}} \
        </option> \
      </select> \
    </span> \
    <span v-if="showAmenities" v-for="a in amenityList"> \
      <label :class="classLabel" >{{ getAmenityLabel(a) }}</label> \
      <select :class="jsSelectorAmenities + \' \' + classSearchParamAmenities" :name="a.amenity_category" multiple="multiple" \
        :data-placeholder="getAmenityLabel(a)"> \
      </select> \
    </span> \
    <span> \
      <label :class="classLabel">{{ qtyLabel }}</label> \
      <select name="qty" :class="classSearchParamQty" \
        :data-placeholder="qtyLabel"> \
        <option :value="getQtyValue(n)" v-for="n in qtyMax"> \
          {{ getQtyDescription(n) }} \
        </option> \
      </select> \
    </span> \
  </div> \
  <button name="search" :class="classSearchButton" :disabled="$parent.dstIsValid == 0">{{ buttonLabel }}</button> \
</span> \
  '
});