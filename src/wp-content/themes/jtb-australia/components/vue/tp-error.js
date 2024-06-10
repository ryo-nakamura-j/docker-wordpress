
Vue.component('tp-error', {
  props: ['errorList'],
  mounted: function() {
    _.forEach( this.errorList, function(e) {
      console.log( e );
    });
  },
  template: '\
    <!-- tp-error --> \
    <div class="col-xs-12"> \
      <h3 v-for="e in errorList">{{e}}</h3> \
    </div> \
  '
});