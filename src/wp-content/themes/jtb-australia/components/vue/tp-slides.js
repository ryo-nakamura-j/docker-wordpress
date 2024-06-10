
Vue.component('tp-slides', {
  props: ['tpSrcList', 'tpId', 'tpUrl'],
  data: function() {
  		return {
  			srcList: this.tpSrcList.slice(0),
        errorSrcList: [],
  		}
  },
  computed: {
    curSrcList: {
      get: function() { 
        var list = [];
        for ( var i in this.tpSrcList ) {
          var v = this.tpSrcList[i];
          if ( this.errorSrcList.indexOf( v ) < 0 )
            list.push( v );
        }
        if ( list.length == 0 )
          list.push( this.helper.defaultImageURL() );
        return list;
      }
    }
  },
  methods: {
  	onError: function( idx ) {
      this.errorSrcList.push( this.tpSrcList[idx] );
  	}
  },
  template: '\
    <!-- tp-slides --> \
    <div> \
      <a :href="tpUrl"> \
        <div :id="tpId" class="carousel slide" data-ride="carousel"> \
          <ol class="carousel-indicators" v-if="curSrcList.length > 1" > \
            <li :data-target="\'#\' + tpId" v-for="(s, idx) in curSrcList" :data-slide-to="idx" :class="{ active: idx==0 }"></li> \
          </ol> \
          <div class="carousel-inner" role="listbox"> \
              <div v-for="(s, idx) in curSrcList" :class="{ item: true, active: idx==0 }"> \
                <img  \
                :src="curSrcList[idx]"  \
                class="fullwidth" \
                @error="onError(idx)"/> \
              </div> \
          </div> \
        </div> \
      </a> \
      <a v-if="curSrcList.length > 1" class="left carousel-control" :href="\'#\' + tpId" role="button" data-slide="prev"> \
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> \
        <span class="sr-only">Previous</span> \
      </a> \
      <a v-if="curSrcList.length > 1" class="right carousel-control" :href="\'#\' + tpId" role="button" data-slide="next"> \
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> \
        <span class="sr-only">Next</span> \
      </a> \
    </div> \
  '
});