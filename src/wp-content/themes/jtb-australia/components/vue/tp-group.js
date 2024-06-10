
Vue.component('tp-group', {
    props: {
    },
    data: function() {
        return {}
    },
    methods: {
    },
    template: ' \
    <div class="tp-group"> \
        <div class="tp-header">\
            <slot name="tp-header" class="tp-header"></slot> \
        </div> \
        <div class="tp-body">\
            <slot name="tp-body" class="tp-body"></slot> \
        </div> \
    </div> \
    '
});