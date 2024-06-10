
Vue.component('product-search-results', {
  props: ['results', 'inputData', 'searchConfigs', 'sortOrderMostPopularDbIndex', 'maxItemPerPage'],
  data: function() {
    var SORT_PRICE_HIGH_TO_LOW = "SORT_PRICE_HIGH_TO_LOW";
    var SORT_PRICE_LOW_TO_HIGH = "SORT_PRICE_LOW_TO_HIGH";
    var SORT_MOST_POPULAR = "SORT_MOST_POPULAR;"
    return {
      SORT_PRICE_HIGH_TO_LOW: SORT_PRICE_HIGH_TO_LOW,
      SORT_PRICE_LOW_TO_HIGH: SORT_PRICE_LOW_TO_HIGH,
      SORT_MOST_POPULAR: SORT_MOST_POPULAR,
      curPageIndex: 1,
      unfoldRange: 2,
      sortOrder: SORT_MOST_POPULAR,
    }
  },
  computed: {
      totalPageCount: { get: function() { 
        return ( this.results.length - (this.results.length % this.maxItemPerPage) ) / this.maxItemPerPage + 1 
      } },
      unfoldIndexList: { get: function() {
        var rlt = [];
        var from = Math.max( 2, this.curPageIndex - this.unfoldRange );
        var to = Math.min( this.totalPageCount - 1, this.curPageIndex + this.unfoldRange );
        for ( var i = from; i <= to; i++ ) {
          rlt.push( i );
        }
        return rlt;
      }}
  },
  methods: {
    isFolded: function( n ) {
      if ( n <= 1 || n >= this.totalPageCount )
        return false;
      if ( Math.abs( n - this.curPageIndex ) <= this.unfoldRange )
        return false;
      return true;
    },
    changePageTo: function( n, event ) {
      event.preventDefault();
      if ( n < 1 || n > this.totalPageCount )
        return;
      this.curPageIndex = n;
    },
    getFilteredResult: function() {
      var rlt = [];
      var first = (this.curPageIndex - 1) * this.maxItemPerPage;
      var last = Math.min( this.curPageIndex * this.maxItemPerPage );
      var sortedResult = this.getSortedResult( this.results );
      for ( var i = first; i < last; i++ ) {
        if ( sortedResult[i] )
          rlt.push( sortedResult[i] );
      }
      return rlt;
    },
    getSortedResult: function() {
      var v = this;
      switch ( this.sortOrder ) {
        case this.SORT_PRICE_LOW_TO_HIGH:
          return _.sortByOrder( this.results, function(e) { 
            return e.availability.TotalPrice
          }, ['asc'] );
          break;
        case this.SORT_PRICE_HIGH_TO_LOW:
          return _.sortByOrder( this.results, function(e) { 
            return e.availability.TotalPrice
          }, ['desc'] );
          break;
        case this.SORT_MOST_POPULAR:
        default:
          var l = _.sortByOrder( this.results, function(e) { 
            return e.product.name;
          }, ['asc'] );
          return _.sortByOrder( l, function(e) {
            // Find analysis field to sort on
            var aList = e.product.analyses; 
            var a = 0;
            _.forEach( aList, function( aa ) {
              if ( aa.substring(0,3) == "db" + v.sortOrderMostPopularDbIndex )
                a = parseInt( aa.substring( 4, aa.length ) ) || 0;
            })
            return [a];
          }, ['desc'] );
          break;
      }
    }
  },
  template: ' \
    <!-- product-search-result-component --> \
    <div v-if="results == null" style="width:100%;text-align:center"> \
      <img :src="helper.loadingImage()" /> \
    </div> \
    <div v-else> \
      <div class="search__results"> \
         <p class="results__01"> <span>{{results.length}}</span> {{dataListLabel("results_in", "results in")}} <span>{{inputData.dst}}</span></p> \
         <div class="clearfix results__border"> \
            <p class="results__03"> \
               <select name="select_pupular" class="custom_select" v-model="sortOrder"> \
                  <option :value="SORT_MOST_POPULAR">{{dataListLabel("most_popular_ranking", "Most Popular Ranking")}}</option> \
                  <option :value="SORT_PRICE_LOW_TO_HIGH">{{dataListLabel("total_price_low_to_high", "Total Price Low to High")}}</option> \
                  <option :value="SORT_PRICE_HIGH_TO_LOW">{{dataListLabel("total_price_high_to_low", "Total Price High to Low")}}</option> \
               </select> \
            </p> \
         </div> \
      </div> \
      <div class="search__list"> \
        <div v-for="(r,idx) in getFilteredResult()"> \
          <slot :tp-result="r" :tp-index="idx"></slot>\
        </div> \
      </div> \
      <div class="search__paging clearfix"> \
         <p class="floatL"> \
            {{results.length}} {{dataListLabel("results_in", "results in")}} {{inputData.dst}} \
         </p> \
         <div class="floatR wp-pagenavi"> \
            <a class="last" href="" @click="changePageTo(curPageIndex - 1, $event)">Prev</a> \
            <span v-if="1 == curPageIndex" class="current">1</span> \
            <a v-else class="page larger" href="" @click="changePageTo(1, $event)">1</a>\
            <span v-if="isFolded(2)" class="extend">...</span>\
            \
            <a v-for="n in unfoldIndexList" v-if="n < curPageIndex" href="" @click="changePageTo(n, $event)">{{n}}</a>\
            <span v-if="curPageIndex != 1 && curPageIndex != totalPageCount" class="current">{{curPageIndex}}</span>\
            <a v-for="n in unfoldIndexList" v-if="n > curPageIndex" href="" @click="changePageTo(n, $event)">{{n}}</a>\
            \
            <span v-if="isFolded(totalPageCount-1)" class="extend">...</span>\
            <span v-if="curPageIndex == totalPageCount && totalPageCount != 1" class="current">{{totalPageCount}}</span> \
            <a v-else-if="totalPageCount != 1" class="page larger" href="" @click="changePageTo(totalPageCount, $event)">{{totalPageCount}}</a>\
            \
            <a class="last" href="" @click="changePageTo(curPageIndex + 1, $event)">Next</a> \
         </div> \
      </div> \
    </div> \
  '
});