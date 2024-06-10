
Vue.component('tp-pikaday-responsive', {
    props: {
        tpClass: { default: '' },
        tpName: { default: '' },
        value: { default: null },
        options: { default: null },
        datePickerClassIdentifier: { default: "datepicker" },
        defaultEmpty: { default: true },
    },
    data: function() {
        return { 
            picker: null,
            isInitialized: false,
        }
    },
    watch: {
        options: function() {
            var vm = this;
            if ( vm.options ) 
                vm.init();
            if ( vm.isInitialized ) {
                // Set min and max date
                if ( vm.options.minDate && vm.options.minDate.toDate 
                    && ( vm.options.minDate.isValid == null || vm.options.minDate.isValid() ) ) {
                    vm.picker.setMinDate( vm.options.minDate.toDate() )
                    if ( moment(vm.value) < vm.options.minDate ) 
                        vm.picker.setDate( vm.options.minDate.toDate() );
                }
                if ( vm.options.maxDate && vm.options.maxDate.toDate
                    && ( vm.options.maxDate.isValid == null || vm.options.maxDate.isValid() ) ) {
                    vm.picker.setMaxDate( vm.options.maxDate.toDate() )
                    if ( moment(vm.value) > vm.options.maxDate ) 
                        vm.picker.setDate( vm.options.maxDate.toDate() );
                }
            }
        }
    },
    mounted: function() {
        var vm = this;
        if ( vm.options )
            vm.init();
    },
    computed: {
        classInput: function() {
            return this.tpClass + " " + this.datePickerClassIdentifier;
        },
    },
    methods: {
        init: function() {
            var vm = this;
            var el = this.$refs.el;
            if ( vm.isInitialized )
                return;
            vm.isInitialized = true;
            vm.picker = pikadayResponsive( $(el), _.extend({}, TourplanRetailUtilities.PIKADAYDEFAULTS, vm.options ) );
            $(el).on('change', function() {
                vm.$emit('input', vm.picker.value);
            });
            if ( vm.defaultEmpty )
                // We have to set an empty date, so the date picker will initialize
                vm.picker.setDate( "" );
            else
                vm.picker.setDate( vm.options.pikadayOptions.defaultDate );
        },
    },
    template: ' \
    <input type="text" :name="tpName" ref="el" :class="classInput"> \
    '
});