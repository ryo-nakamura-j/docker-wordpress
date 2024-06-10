var tpFieldRetainMixin = {
	computed: {
		COOKIE_PREFIX: function() { return "TP_COOKIE_PREFIX_"; },
		COOKIE_EXPIRES_DATE: function() { return 3; },
		FIELD_RETAIN_EXCLUDE_LIST: function() { return ["email"]; },
	},
	methods: {
		isExcluded: function( key ) {
			var vc = this;
			_.forEach( this.FIELD_RETAIN_EXCLUDE_LIST, function(v) {
				if ( key.indexOf(v) > -1 ) {
					// Remove cookie, just in case we saved it in another site version
					vc.clearField( key );
					return true;
				}
			});
			return false;
		},
		saveField: function( key, e ) {
			if ( this.isExcluded( key ) )
				return;
			// Save event value to cookie
			this.saveFieldValue( key, e.target.value );
		},
		saveCheckBox: function( key, e ) {
			if ( this.isExcluded( key ) )
				return;
			// Save event value to cookie
			this.saveFieldValue( key, e.target.checked ? "1" : "0" );
		},
		saveFieldValue: function( key, v ) {
			if ( this.isExcluded( key ) )
				return;
			// Save to cookie
			if ( !Cookies )
				return function() {};
			if ( _.isEmpty( v ) )
				Cookies.remove( this.COOKIE_PREFIX + key );
			else
				Cookies.set( this.COOKIE_PREFIX + key, v, { expires: this.COOKIE_EXPIRES_DATE });
		},
		loadField: function( key ) {
			if ( this.isExcluded( key ) )
				return;
			// Load from cookie
			if ( !Cookies )
				return "";
			return Cookies.get( this.COOKIE_PREFIX + key );
		},
		clearField: function( key ) {
			Cookies.remove( this.COOKIE_PREFIX + key );
		}
	}
}