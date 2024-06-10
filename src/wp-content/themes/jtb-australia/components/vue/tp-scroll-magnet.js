
Vue.component('tp-scroll-magnet', {
    props: {
        tpOffsetTopPad: { default: null },
        tpBoundsElementSelector: { default: null },
        isViewMobile: { default: false },
        minBottomHeight: { default: 200 },
    },
    data: function() {
        return { 
            servicelineSectionHeight: this.minBottomHeight,
        }
    },
    methods: {
        init: function() {
            // Fix scroll-magnet effect init issue
            vm = this;
            vm.servicelineSectionHeight = Math.max( 
                $( this.tpBoundsElementSelector ).height(), this.minBottomHeight );
            Vue.nextTick( function() {
                Vue.nextTick( function() {
                    vm.$refs.scrollContainer.getElementPosition();
                    vm.$refs.scrollItem.setMagnetStatus(vm.$refs.scrollItem.nearestContainer),
                    vm.$refs.scrollItem.setMagnetWidth();
                });
            });
        }
    },
    template: ' \
    <div :style="(isViewMobile? \'\' : (\'height:\' + servicelineSectionHeight + \'px;\' + \' margin-top: -75px;\'))"> \
        <scroll-magnet-container v-if="!isViewMobile" ref="scrollContainer"> \
            <scroll-magnet-item ref="scrollItem"> \
                <div style="height:75px;"> \
                    &nbsp; \
                </div> \
                <slot></slot> \
            </scroll-magnet-item> \
        </scroll-magnet-container> \
        <div v-else> \
            <slot></slot> \
        </div> \
    </div> \
    '
});