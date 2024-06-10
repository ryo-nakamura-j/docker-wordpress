<!-- tp-header-ctrl -->
<script>
    var headerVueData = null;
    $(document).ready( function() {
        var sectionConfig = <?php echo json_encode($this->sectionConfig); ?>;
        var rootId = '<?php echo $this->rootId?>';
        var cartUrl = '<?php echo site_url(tp_get_url('tp_cart_url'))?>';
        headerVueData = new Vue({
            mixins: [tpCachedData],
            el: rootId,
            data: {
                itineraryItemCount: 0,
                helper: templatesHelper,
                sectionConfig: sectionConfig,
                isFixedHeader: false,
                __hNavi: 0,
                sticky: 0,
            },
            mounted: function() {
                var vm = this;
                $(rootId).removeAttr('hidden');
                vm.__hNavi = $("#headerWrap").outerHeight();
                vm.sticky = document.getElementById("navi").offsetTop;
                vm.helper.getCart(function(newCart) {
                    var list = newCart.servicelines;
                    if ( list )
                        vm.itineraryItemCount = list.length;
                    else
                        vm.itineraryItemCount = 0;
                }, cartUrl);
            },
            methods: {
                matchUrl: function( a, b ) {
                    return a != null && b != null 
                        && a.indexOf( b ) > -1
                        && a.substring( a.indexOf( b ) + b.length ).indexOf('/') == -1;
                },
                scrollHeader: function() {
                    if ($(window).width() > 768) {
                        if (window.pageYOffset >= this.sticky) {
                            this.isFixedHeader = true;
                            $('body').css({ 'padding-top': this.__hNavi + 'px' });
                        } else {
                            this.isFixedHeader = false;
                            $('body').css({ 'padding-top': 0 + 'px' });
                        }
                    } else {
                        if (window.pageYOffset > $('.header__title').outerHeight()) {
                            $('body').css({ 'padding-top': this.__hNavi + 'px' });
                            this.isFixedHeader = true;
                        } else {
                            this.isFixedHeader = false;
                            $('body').css({ 'padding-top': 0 + 'px' });
                        }
                    }
                }
            },
        });

        $(window).on('scroll', function() {
            headerVueData.scrollHeader();
        });
    });
</script>