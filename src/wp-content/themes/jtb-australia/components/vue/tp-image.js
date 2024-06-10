
Vue.component('tp-image', {
  props: ['srcImage', 'onErrorSrc', 'hrefUrl', 'classImage'],
  data: function() {
  	return {
  		curSrcImage: this.srcImage
  	};
  },
  methods: {
  	onErrorOptionImage: function() {
  		console.log( "image not found: " + this.srcImage );
  		this.curSrcImage = this.onErrorSrc;
  	}
  },
  template: '\
  <!-- tp-image --> \
	<a :href="hrefUrl"> \
	  <img :class="classImage" :src="curSrcImage" @error="onErrorOptionImage"> \
	</a> \
  '
});