// Default configuration
var prefix = 'v-collapse';
var basename = 'collapse';

var defaults = {
    'prefix' : prefix,
    'basename' : basename,
    'togglerClassDefault': prefix + '-toggler',
    'contentClassDefault': prefix + '-content',
    'contentClassEnd': prefix + '-content-end'
};

// Global toggle methods

var toggleElement = function (target, config) {
    $(target).toggleClass(config.contentClassEnd);
};

var closeElement = function (target, config) {
    $(target).removeClass(config.contentClassEnd);
};

var openElement = function (target, config) {
    $(target).addClass(config.contentClassEnd);
};

var CollapseWrapper = {
    data: function () {
        return {
            nodes: {},
            status: false,
        }
    },
    
    props: ['active'],

    // status watcher - change toggle element when status changes
    watch: {
        active: function(status){
              if ( status != null ) {
                this.status = status;
            }
        },

        status: function (new_value, old_value) {
            this.$emit('onStatusChange', {vm: this, status: new_value, old_status: old_value});
            if (this.$parent.onlyOneActive === false) {
                toggleElement(this.nodes.content, this.$options.$vc.settings);
            } else {
                if (new_value === true && old_value === false) {
                    var active = this.$parent.$children.filter(function (el) {
                        return el.status === true;
                    });
                    if (active.length > 1) {
                        active.forEach(function (el) {
                            el.close();
                            closeElement(el.nodes.content, this.$options.$vc.settings);
                        }.bind(this))
                    }
                    openElement(this.nodes.content, this.$options.$vc.settings);
                    this.open();
                } else if (old_value === true && new_value === false) {
                    closeElement(this.nodes.content, this.$options.$vc.settings);
                    this.close();
                }
            }

        }
    },

    // collapse basic instance methods

    methods: {
        toggle: function () {
            this.$emit('beforeToggle', this);
            this.status = !this.status;
            this.$emit('afterToggle', this);
        },
        close: function () {
            this.$emit('beforeClose', this);
            this.status = false;
            this.$emit('afterClose', this);
        },
        open: function () {
            this.$emit('beforeOpen', this);
            this.status = true;
            this.$emit('afterOpen', this);
        },
    },

    // mounting

    mounted: function () {
        var vm = this;
        this.nodes.toggle = this.$el.querySelector('.' + this.$options.$vc.settings.togglerClassDefault);
        this.nodes.content = this.$el.querySelector('.' + this.$options.$vc.settings.contentClassDefault);
        this.$emit('afterNodesBinding', {vm: this, nodes: this.nodes});
        if(this.nodes.toggle !== null){
            this.nodes.toggle.addEventListener('click', function() {
                vm.toggle();
            });
        }
        if ( this.active != null ) {
                this.status = this.active;
        }
    },

    template: ' \
    <div :class="\'vc-\' + $options.$vc.settings.basename"> \
        <slot></slot> \
    </div> \
    '
};

var CollapseGroup = {
    data: function () {
        return {}
    },

    props: {
        onlyOneActive: {
            default: false,
            type: Boolean
        }
    },

    // computed props for accessing elements
    computed: {
        elements : function () {
            return this.$children;
        },
        elements_count : function () {
            return this.$children.length;
        },
        active_elements: function () {
            return this.$children.filter(function (el) {
                return el.status === true;
            })
        }
    },
    methods: {
        closeAll: function () {
            this.$children.forEach(function (el) {
                el.close();
            })
        },
        openAll: function () {
            this.$children.forEach(function (el) {
                el.open();
            })
        }
    },

    template: ' \
    <div class="v-collapse-group"> \
        <slot></slot> \
    </div> \
    '
};

var VueCollapse = {};
VueCollapse.install = function (Vue, options) {

    // merge configs
    var settings = $.extend(defaults, options);

    // creating required components
    Vue.component(settings.prefix + '-wrapper', CollapseWrapper);
    Vue.component(settings.prefix + '-group', CollapseGroup);

    // creates instance of settings in the Vue
    Vue.mixin({
        created: function () {
            this.$options.$vc = {
                settings : settings
            };
        }
    });

    // content directive
    Vue.directive(settings.basename + '-content', {
        // assigning css classes from settings
        bind: function(el, binding, vnode, oldVnode) {
            $(vnode.elm).addClass(vnode.context.$options.$vc.settings.contentClassDefault);
        }
    });

    // toggler directive
    Vue.directive(settings.basename + '-toggle', {

        // adding toggle class
        bind: function(el, binding, vnode, oldVnode) {
            $(vnode.elm).addClass(vnode.context.$options.$vc.settings.togglerClassDefault);
        },

        // Creating custom toggler handler
        inserted: function(el, binding, vnode, oldVnode) {
            if (binding.value != null) {
                vnode.elm.addEventListener('click', function () {
                    vnode.context.$refs[binding.value].status = !vnode.context.$refs[binding.value].status;
                }.bind(this));
            }
        }
    });
};
if (typeof window !== 'undefined' && window.Vue) {
    window.Vue.use(VueCollapse)
}