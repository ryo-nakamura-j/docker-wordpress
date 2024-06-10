<?php $this->expectedController( "TpProductSearch" )?>

<div id="tp_search_section" class="accomPage" hidden>
  <div class="container">
    <h2 class="ver__h2">{{dataListLabel("ryokan_and_hotels", "Ryokan & Hotels")}}</h2>
  </div>
   <div class="ver__top">
      <div class="top__mainImg">
         <div class="container">
            <div class="mainImg__box">
              <form class="" action="#" method="post" class="frmTop clearfix">
                <!-- Tourplan Search Panel -->
                <span id="tp_search_panel">
                  <product-search-panel v-bind:section-config="sectionConfig" show-to-date="true" show-amenities="false" qty-postfix="DOM_MODIFICATION" :is-dst-auto-hide="isDstAutoHide"/>
                </span>
              </form>
            </div>
         </div>
      </div>
      <div class="searchPage">
         <div class="container">
            <product-search-results :results="results" :search-configs="sectionConfig.search_config" :input-data="inputData" :sort-order-most-popular-db-index="sectionConfig.sort_order_most_popular_db_index" :max-item-per-page="sectionConfig.pagination_max_item_on_page">
              <!-- each slot contains a single result -->
              <template slot-scope="slotProps">
                <accom-detail-panel :tp-result="slotProps.tpResult" :tp-index="slotProps.tpIndex" :search-configs="sectionConfig.search_config" tp-class="row"/>
              </template>
            </product-search-results>
         </div>
      </div>
   </div>
</div>
<link href="<?php echo get_template_directory_uri() ?>/templates/css/version.css" rel="stylesheet">
<link href="<?php echo get_template_directory_uri() ?>/templates/css/tour.css" rel="stylesheet">
<!-- Owl Stylesheets -->
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/templates/css/owl.carousel.min.css">
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/templates/css/owl.theme.default.min.css">
<link href='//fonts.googleapis.com/css?family=Droid+Sans:400,700|Arimo|Signika:400,600' rel='stylesheet' type='text/css'>
<link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/templates/css/jquery.multiselect.css">
<script src="<?php echo get_template_directory_uri() ?>/templates/js/jquery.multiselect.js"></script>
<script src="<?php echo get_template_directory_uri() ?>/templates/js/owl.carousel.js"></script>
<script>
   function onVueSearchResultMounted(){
   }
</script>