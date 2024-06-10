// Inspired by https://github.com/tecbeast42/autocomplete-vue

Vue.component('tp-autocomplete', {
    data: function() {
        return {
            search: '',
            focused: false,
            mousefocus: false,
            selectedIndex: 0
        };
    },
    computed: {
        filteredEntries: function() {
            var vm = this;
            if (this.search.length <= this.threshold) {
                return [];
            } else {
                return this.entries.filter(function(entry) {
                    if (vm.ignoreCase) {
                        return entry[vm.property].toLowerCase().indexOf(vm.search.toLowerCase()) > -1;
                    }
                    return entry[vm.property].indexOf(vm.search) > -1;
                });
            }
        },
        hasSuggestions: function() {
            if (this.search.length <= this.threshold) {
                return false;
            }

            return this.filteredEntries.length > 0;
        },
        showSuggestions: function() {
            if (!this.hasSuggestions) {
                return false;
            }

            if (this.focused || this.mousefocus) {
                return true;
            }

            return false;
        },
        entries: function() {
            if (this.list !== undefined) {
                return this.list;
            } 
            return [];
        }
    },
    created: function() {
        this.search = this.value;
    },
    methods: {
        select: function(index) {
            var vm = this;
            if (this.hasSuggestions) {
                this.search = this.filteredEntries[index][this.property];

                if (this.autoHide) {
                    this.mousefocus = false;
                    this.focused = false;
                    this.$refs.input.blur();
                } else {
                    this.$nextTick(function() {
                        vm.$refs.input.focus();
                    });
                }
            }
        },
        moveUp: function() {
            if ((this.selectedIndex - 1) < 0) {
                this.selectedIndex = this.filteredEntries.length - 1;
            } else {
                this.selectedIndex -= 1;
            }
        },
        moveDown: function() {
            if ((this.selectedIndex + 1) > (this.filteredEntries.length - 1)) {
                this.selectedIndex = 0;
            } else {
                this.selectedIndex += 1;
            }
        },
        selectedClass: function(index) {
            if (index === this.selectedIndex) {
                return this.classPrefix + '__selected';
            }

            return '';
        },
        getListAjax: function() {
            return [];
        }
    },
    props: {
        classPrefix: {
            type: String,
            required: false,
            default: 'autocomplete',
        },
        url: {
            type: String,
            required: false,
        },
        requestType: {
            type: String,
            required: false,
            default: 'get',
        },
        list: {
            type: Array,
            required: false,
        },
        placeholder: {
            type: String,
            required: false,
        },
        property: {
            type: String,
            required: false,
            default: 'name',
        },
        inputClass: {
            type: String,
            required: false,
        },
        required: {
            type: Boolean,
            required: false,
            default: false,
        },
        ignoreCase: {
            type: Boolean,
            required: false,
            default: true,
        },
        threshold: {
            required: false,
            default: 0,
        },
        value: {
            required: false,
            default: '',
        },
        autoHide: {
            type: Boolean,
            required: false,
            default: false,
        }
    },
    watch: {
        filteredEntries: function(value) {
            if (this.selectedIndex > value.length - 1) {
                this.selectedIndex = 0;
            }
        },
        search: function(value) {
            this.$emit('input', value);
            this.$emit('onchange', value);
        },
        value: function(newValue) {
            this.search = newValue;
        }
    },
	template: '\
    <span :class="classPrefix" @mousedown="mousefocus = true" @mouseout="mousefocus = false"> \
        <input type="text" @blur="focused = false" @focus="focused = true" \
            v-model="search" :placeholder="placeholder" :class="inputClass" \
            @keydown.down.prevent.stop="moveDown()" \
            @keydown.up.prevent.stop="moveUp()" \
            @keydown.enter.prevent.stop="select(selectedIndex)" \
            @keydown.tab="mousefocus = false" \
            ref="input" \
            :required="required"> \
        <div v-if="showSuggestions" :class="classPrefix + \'__suggestions\'"> \
        <div v-for="(entry, index) in filteredEntries" \
                @click="select(index)" \
                :class="[classPrefix + \'__entry\', selectedClass(index)]"> \
                {{ entry[property] }} \
            </div> \
        </div> \
    </span> \
	'
});