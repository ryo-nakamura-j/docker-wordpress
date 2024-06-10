var tpCachedData = {
	computed: {
		CACHE_KEY_ITINERARY_ITEM_COUNT: function() { return "ITINERARY_ITEM_COUNT"; },
		COOKIE_CACHE_PREFIX: function() { return "TP_CACHED_DATA_PREFIX_"; },
		COOKIE_CACHE_EXPIRES_DATE: function() { return 3; },
	},
	methods: {
		saveCacheValue: function( key, v ) {
			// Save to cookie
			if ( !Cookies )
				return function() {};
			Cookies.set( this.COOKIE_CACHE_PREFIX + key, v, { expires: this.COOKIE_CACHE_EXPIRES_DATE });
		},
		loadCacheValue: function( key ) {
			// Load from cookie
			if ( !Cookies )
				return "";
			return Cookies.get( this.COOKIE_CACHE_PREFIX + key );
		},
		clearCacheValue: function( key ) {
			Cookies.remove( this.COOKIE_CACHE_PREFIX + key );
		}
	}
}