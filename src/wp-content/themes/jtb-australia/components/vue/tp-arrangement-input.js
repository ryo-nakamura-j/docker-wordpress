
Vue.component('tp-arrangement-input', {
  props: ['details', 'classContainer', 'overWriteRequiredTrue', 'overWriteRequiredFalse', 'validationString'],
  data: function() {
  	return {
  	};
  },
  methods: {
  },
  computed: {
    dt: function() {
      return this.details;
    },
    classContainerString: function() {
      return 'arrangement-details' + ' ' 
        + this.classContainer + ' ' 
        + (this.isRequired ? 'required' : '');
    },
    isRequired: function() {
      if ( this.overWriteRequiredTrue === "")
        return true;
      if ( this.overWriteRequiredFalse === "" )
        return false;
      return this.dt.IsRequiredFlag;
    },
  },
  template: '\
  <div :class="classContainerString" :name="dt.ArrangementID" :validation="validationString"> \
    <div class="col-xs-12 col-md-3 arrangements-col1"> \
      <label :class="isRequired?\'required_asterisk\':\'\'">    \
        {{dt.SelectionCodeMessage}}  \
      </label> \
      <div v-if="!dt.IsInputMessageEmpty" class="tp-mobile-tooltip small"> \
        {{dt.InputMessage}} \
      </div> \
    </div> \
    <div class="col-xs-12 col-md-9 arrangements-col2 " :name="dt.ArrangementID"> \
      <slot></slot> \
      <span v-if="!dt.IsInputMessageEmpty" class="tp-tooltip tp-icon"> \
          <img :src="helper.themeUrl + \'/templates/img/tour_result/icon_question.svg\'" alt="" width="29" height="29"  \:title="dt.InputMessage" >  \
      </span> \
    </div> \
  </div> \
  '
});