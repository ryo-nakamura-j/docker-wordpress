<?php $this->expectedController( "TpProductSearch" )?>

<div id="tp_search_section" class="tourPage" hidden>
   <div class="ver__top">
      <div class="top__mainImg">
         <div class="container">
            <div class="mainImg__box">
               <form class="" action="#" method="post">
                  <!-- Tourplan Search Panel -->
                  <span id="tp_search_panel">
                     <product-search-panel :section-config="sectionConfig"  :is-dst-auto-hide="isDstAutoHide"/>
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
                <day-tours-detail-panel :tp-result="slotProps.tpResult" :tp-index="slotProps.tpIndex" :search-configs="sectionConfig.search_config" tp-class="row"/>
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
     // $('.item__slider.owl-carousel').owlCarousel({
     //   center: true,
     //    loop:true,
   
     //    margin:0,
     //    nav:true,
     //    responsive:{
     //        0:{
     //            items:1
     //        },
     //        600:{
     //            items:1
     //        },
     //        1000:{
     //            items:1
     //        }
     //    }
     // })
   }
</script>