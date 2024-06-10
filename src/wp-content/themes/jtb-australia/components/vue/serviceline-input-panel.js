
Vue.component('serviceline-input-panel', {
    props: {
        value: { default: null },
        servicelineWrapper: { default: null },
        servicelineIndex: { default: -1 },
        classPrefix: { default: 'serviceline-default-class' },
        hasPassengerSection: { default: false },
        hasPreferenceSection: { default: false },
        hasNoticeSection: { default: false },
        hasInfoSection: { default: false },
        hadUpdateButton: { default: true },
        isHidden: { default: false },
        datePickerOptions: { default: null },
    },
    data: function() {
        return {
            isExpanded: true,
        }
    },
    computed: {
        s: function() { return this.value; },
        sIdx: function() { return this.servicelineIndex; },
        classContainer: function() { 
            return "row product" + ' ' 
            + this.classPrefix + ' ' 
            + this.classPrefix + '_' + this.s.productid + ' ' 
            + 'serviceline_' + this.sIdx 
        },
        noticeSectionData: function() {
            return this.helper.serviceButtonConfig( this.s.servicetype, 'itineraryNoticeLabel' );
        },
    },
    mounted: function() {
        var vm = this;
        if ( vm.s.configs.length > 0 ) {
            _.forEach( vm.s.configs[0].pax, function(p) {
                // Initialize nationality
                if ( vm.getPaxNat(vm.s).length <= 1 )
                    p.nationality =  vm.getPaxNat(vm.s)[0].Value;
                p.japaneseonlytermsandconditions = false;
            });
        }
        this.collapseOpen();
    },
    methods: {
        getAgeString: function(p) {
            var ageString = "";
            var paxType = p.paxtype;
            switch (paxType) {
                case "A":  ageString += !_.isEmpty(this.s.adultages) ? this.dataListLabel("age", "Age") + " " + this.s.adultages : ""; break;
                case "C":  ageString += !_.isEmpty(this.s.childages) ? this.dataListLabel("age", "Age") + " " + this.s.childages : ""; break;
                case "I":  ageString += !_.isEmpty(this.s.infantages) ? this.dataListLabel("age", "Age") + " " + this.s.infantages : ""; break;
            }
            return ageString;
        },
        getPaxNat: function(s) {
            var kv = this.helper.serviceButtonConfig(s.servicetype, "paxNationalities").split(',');
            kvPair = [];
            kv.forEach( function( kkvv ) {
                kkvvSplit = kkvv.split('=');
                if ( kkvvSplit.length < 2 )
                    kvPair.push({ Value: kkvv, Display: kkvv }) ;
                else
                    kvPair.push({ Value: kkvvSplit[1], Display: kkvvSplit[0] }) ;
            })
            return kvPair;
        },
        getPaxNatKeyValueMap: function(s) {
            return this.helper.serviceButtonConfig(s.servicetype, "paxNationalities").split(',');
        },
        getTitle: function(s) {
            return this.helper.getServiceButtonMapConfig("", "titles", "");
        },
        collapseClose: function() {
            if ( this.$refs.collapseWrapper )
                this.$refs.collapseWrapper.close();
        },
        collapseOpen: function() {
            if ( this.$refs.collapseWrapper )
                this.$refs.collapseWrapper.open();
        },
        collapseStatusChanged: function( e ) {
            var vm = this;
            this.isExpanded = e.status;
            vm.$emit( 'height-changed', e );
            // Collapse effect will take a bit of time ( defined in vue2-collapse.css)
            setTimeout( function() {
                vm.$emit( 'height-changed', e );
            }, 400);
        },
        updatePaxConfig: function( idx, field, value ) {
            var rlt = _.extend( {}, this.value );
            rlt.configs[0].pax[idx][field] = value;
            this.$emit('input', rlt);
        },
        updateConfig: function( field, value ) {
            var rlt = _.extend( {}, this.value );
            rlt[field] = value;
            this.$emit('input', rlt);
        },
    },
    template: ' \
    <!-- serviceline-input-panel --> \
    <span :id="servicelineWrapper.jsServicelineId"> \
        <div :class="classContainer" v-if="!isHidden"> \
            <v-collapse-wrapper ref="collapseWrapper" v-on:onStatusChange="collapseStatusChanged"> \
                <div class="col-xs-12" v-collapse-toggle> \
                    <h4 class="heading clearfix"> \
                        <div class="row header-line-1"> \
                            <slot :line="s" :wrapper="servicelineWrapper" name="header-line-1"></slot>\
                        </div> \
                        <span :class="\'toggle-arrow \' + (!isExpanded ? \'toggle-arrow-up\' : \'toggle-arrow-down\') "></span> \
                        <div class="row"> \
                            <div class="col-xs-12 col-sm-12"> \
                                <span class="pull-right"> \
                                    <small class="bookingFeeLabel"> \
                                        {{servicelineWrapper.bookingFeeLabel}} \
                                    </small> \
                                </span> \
                            </div> \
                        </div> \
                        <slot :line="s" :wrapper="servicelineWrapper" name="header-line-2"></slot>\
                    </h4> \
                </div> \
                <div class="col-xs-12 v-collapse-content"> \
                    <div class="detail_section"> \
                        <div v-if="hasPreferenceSection" class="preferenceSection"> \
                            <div class="row"> \
                                <div class="col-xs-12 col-md-12 notice_section" v-html="noticeSectionData"> \
                                </div> \
                            </div> \
                            <div class="row"> \
                                <div class="col-xs-12 col-md-12 required"> \
                                    <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "preference1Label")}} \
                                    </label> \
                                </div> \
                                <div class="col-xs-12 col-md-6 required"> \
                                    <tp-pikaday-responsive tp-name="preference1" tp-class="form-control" :value="s.preference1" :options="datePickerOptions" v-on:input="updateConfig(\'preference1\',$event)"/> \
                                </div> \
                            </div> \
                        </div> \
                        <div v-if="hasNoticeSection" class="notice_section"> \
                            <div class="row"> \
                                <div class="col-xs-12" v-html="noticeSectionData"> \
                                </div> \
                            </div> \
                        </div> \
                        <div v-if="hasPassengerSection" class="passenger_section"> \
                            <div class="row"> \
                                <!-- Passenger Section --> \
                                <div v-if="servicelineWrapper.qtyConfig == \'roombased\'" class="col-xs-12"> \
                                    <div v-for="c in s.configs" class="configSection" :id="c.jsRoomConfigId">\
                                        <input type="hidden" name="type"/> \
                                        <input type="hidden" name="adults"/> \
                                    </div> \
                                </div> \
                                <div v-if="servicelineWrapper.qtyConfig == \'paxbased\'" class="col-xs-12"> \
                                    <div class="configSection"> \
                                        <div class="row passenger" v-for="(paxConfig,idx) in s.configs[0].pax"  :id="paxConfig.jsPaxConfigId"> \
                                            <div class="col-xs-12"> \
                                                <h4 class="passenger_heading">{{dataListLabel("person", "Person")}} {{idx+1}} ({{getAgeString( paxConfig )}})</h4> \
                                                <div class="row"> \
                                                    <div class="col-xs-12 col-sm-12 required" v-if="helper.serviceButtonConfigContains(s.servicetype, \'paxSections\', \'title\')"> \
                                                        <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "titleLabel")}} \
                                                        </label> \
                                                        <select name="title" :value="paxConfig.title" class="form-control tp-shorter-input" v-on:change="updatePaxConfig(idx,\'title\',$event.target.value)"> \
                                                           <option value="" disabled></option> \
                                                           <option v-for="opt in getTitle()" :value="opt.value">{{opt.label}}</option> \
                                                        </select> \
                                                    </div> \
                                                    <div class="col-xs-12 col-sm-6 required" v-if="helper.serviceButtonConfigContains(s.servicetype, \'paxSections\', \'firstNameLang\')"> \
                                                        <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "firstNameLangLabel")}} \
                                                        </label> \
                                                        <tp-safe-text-input tp-name="firstname" tp-class="form-control" v-model="paxConfig.firstnamelang" :is-always-upper-case="false" :tp-character-exception-rule="/[(.*)]/g"/> \
                                                    </div> \
                                                    <div class="col-xs-12 col-sm-6 required" v-if="helper.serviceButtonConfigContains(s.servicetype, \'paxSections\', \'lastNameLang\')"> \
                                                        <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "lastNameLangLabel")}} \
                                                        </label> \
                                                        <tp-safe-text-input tp-name="lastname" tp-class="form-control" v-model="paxConfig.lastnamelang" :is-always-upper-case="false" :tp-character-exception-rule="/[(.*)]/g"/> \
                                                    </div> \
                                                    <div class="col-xs-12 col-sm-6 required" v-if="helper.serviceButtonConfigContains(s.servicetype, \'paxSections\', \'firstName\')"> \
                                                        <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "firstNameLabel")}} \
                                                        </label> \
                                                        <tp-safe-text-input tp-name="firstname" tp-class="form-control" v-model="paxConfig.firstname" /> \
                                                    </div> \
                                                    <div class="col-xs-12 col-sm-6" v-if="helper.serviceButtonConfigContains(s.servicetype, \'paxSections\', \'middleName\')"> \
                                                        <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "middleNameLabel")}} \
                                                        </label> \
                                                        <tp-safe-text-input tp-name="middlename" tp-class="form-control" v-model="paxConfig.middlename" /> \
                                                    </div> \
                                                    <div class="col-xs-12 col-sm-6 required" v-if="helper.serviceButtonConfigContains(s.servicetype, \'paxSections\', \'lastName\')"> \
                                                        <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "lastNameLabel")}} \
                                                        </label> \
                                                        <tp-safe-text-input tp-name="lastname" tp-class="form-control" v-model="paxConfig.lastname" /> \
                                                    </div> \
                                                    <div class="col-xs-12 col-sm-6 required" v-if="helper.serviceButtonConfigContains(s.servicetype, \'paxSections\', \'nationality\')"> \
                                                        <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "countriesLabel")}} \
                                                        </label> \
                                                        <select name="nationality" v-model="paxConfig.nationality" class="form-control" :disabled="getPaxNat(s).length <= 1"  v-on:change="updatePaxConfig(idx,\'nationality\',$event.target.value)"> \
                                                           <option value="" disabled></option> \
                                                           <option v-for="opt in getPaxNat(s)" :value="opt.Value">{{opt.Display}}</option> \
                                                        </select> \
                                                    </div> \
                                                    <div class="col-sm-6 col-xs-12 required" v-if="helper.serviceButtonConfigContains(s.servicetype, \'paxSections\', \'dob\')"> \
                                                        <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "dobLabel")}} \
                                                        </label> \
                                                        <tp-pikaday-responsive tp-name="dob" tp-class="form-control" :value="paxConfig.dob" :options="paxConfig.pikadayOptions" v-on:input="updatePaxConfig(idx,\'dob\',$event)"/> \
                                                    </div> \
                                                    <div class="col-xs-12 col-sm-6 required" v-if="helper.serviceButtonConfigContains(s.servicetype, \'paxSections\', \'passport\')"> \
                                                        <label class="control-label">{{helper.serviceButtonConfig( s.servicetype, "passportLabel")}} \
                                                        </label> \
                                                        <tp-safe-text-input tp-name="passport" tp-class="form-control" v-model="paxConfig.passport" /> \
                                                    </div> \
                                                    <div class="col-xs-12 col-sm-12 col-md-12"> \
                                                    </div> \
                                                    <div v-if="dataList && dataList.japanese_only_terms_and_conditions && paxConfig.nationality == dataList.japanese_only_terms_and_conditions.nationality_and_url[0].text && _.filter( dataList.japanese_only_terms_and_conditions_product_list, { product_id: \'\' + s.productid } ).length > 0" class="col-xs-12 col-sm-6 required tp-checkbox"> \
                                                        <label class="checkbox-inline pull-right"> \
                                                            <input type="checkbox" name="japaneseonlytermsandconditions" v-model="paxConfig.japaneseonlytermsandconditions"/> {{dataListLabel("i_have_read_and_agree", "I have read and agree to the")}} <a :href="dataList.japanese_only_terms_and_conditions.nationality_and_url[0].url" target="_blank">{{dataList.japanese_only_terms_and_conditions.link_name[0].text}}</a> \
                                                        </label> \
                                                    </div> \
                                                    <input type="hidden" name="paxtype" :value="paxConfig.paxtype" /> \
                                                </div> \
                                            </div> \
                                        </div> \
                                    </div> \
                                </div> \
                            </div> \
                        </div> \
                        <div v-if="hasInfoSection" class="info_section"> \
                            <div class="row"> \
                                <div class="col-xs-12 col-sm-6"> \
                                    {{dataListLabel("check_in", "Check In")}}: {{s.datein}} \
                                </div> \
                                <div class="col-xs-12 col-sm-6"> \
                                    {{dataListLabel("check_out", "Check Out")}}: {{s.dateout}} \
                                </div> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="button_section"> \
                        <div class="control_buttons clearfix"> \
                            <div v-if="hadUpdateButton && s.source_url" class="row"> \
                                <div class="col-xs-offset-3 col-xs-5 col-md-offset-7 col-md-3"> \
                                    <span class="pull-right"> \
                                        <button name="update" hidden>{{dataListLabel("update", "Update")}}</button> \
                                        <button name="edit" class="update form-control btn btn-primary tp-secondary-button tp-small-long-button">{{dataListLabel("change_details", "Change Details")}}</button v-on:click="onEdit"> \
                                    </span> \
                                </div> \
                                <div class="col-xs-4 col-md-2"> \
                                    <button name="remove" class="remove form-control btn btn-danger tp-secondary-button tp-small-button pull-right">{{dataListLabel("remove", "Remove")}}</button> \
                                </div> \
                            </div> \
                            <div v-else class="row"> \
                                <div class="col-xs-offset-6 col-md-offset-10 col-xs-6 col-md-2"> \
                                    <button name="remove" class="remove form-control btn btn-danger tp-secondary-button tp-small-button">{{dataListLabel("remove", "Remove")}}</button> \
                                </div> \
                            </div> \
                        </div> \
                    </div> \
                </div> \
            </v-collapse-wrapper> \
        </div> \
    </span> \
    '
});