<!-- Google Tag Manager -->
<?php
require_once APP_PATH.'libs/ua.class.php';
$ua = new UserAgent; ?>
<?php
require_once( dirname( __FILE__ ) . "/../../tp-header-def.php");
$headerUi = new TpHeader();
$headerUi->loadConfig();
$headerUi->init("#headerWrap");
?>
<!-- End Google Tag Manager -->
<header id="headerWrap" :class= "'clearfix header_footer' + ' ' + (isFixedHeader ? 'fixed':'') " hidden>
    <div class="header__title">
        <div class="container">
            <ul class="header__social">
                <li>
                    <h1>{{sectionConfig.secondary_remarks}}</h1></li>
                <li class="pc">
<?php /*
<a v-if="!_.isEmpty(sectionConfig.social_url.facebook)" :href="sectionConfig.social_url.facebook"><img class="over" src="<?php echo APP_ASSETS; ?>img/common/header/ico_fb.svg" alt="Facebook" width="22" height="22" ></a><a  v-if="!_.isEmpty(sectionConfig.social_url.twitter)" :href="sectionConfig.social_url.twitter"><img class="over" src="<?php echo APP_ASSETS; ?>img/common/header/ico_tw.svg" alt="Twitter" width="22" height="22" ></a>
                <a href="https://twitter.com/JTBAust"><img src="https://www.nx.jtbtravel.com.au/wp-content/themes/jtb-australia/components/header_footer/assets/img/common/header/ico_tw.svg" alt="Twitter" width="22" height="22" class="over"></a>

 <a href="/"><img srcset="https://www.nx.jtbtravel.com.au/u/jtb-australia.png, https://www.nx.jtbtravel.com.au/u/jtb-australia@2x.png 2x" class="logo"></a><br> 
*/ ?>
<a href="https://www.facebook.com/JTB.Travel/" target="_blank"><img src="https://www.nx.jtbtravel.com.au/wp-content/themes/jtb-australia/components/header_footer/assets/img/common/header/ico_fb.svg" alt="Facebook" width="33" height="33"></a> <a href="https://www.instagram.com/jtbaustralia/" target="_blank"><img src="https://nx.jtbtravel.com.au/wp-content/uploads/svg/ico_ig.svg" alt="Twitter" width="33" height="33"></a> <a href="https://twitter.com/JTBAust" target="_blank"><img src="https://www.nx.jtbtravel.com.au/wp-content/themes/jtb-australia/components/header_footer/assets/img/common/header/ico_tw.svg" alt="Twitter" width="33" height="33"></a> 



            </li>
            </ul>
            <ul class="header__link pc">
                <li v-for="m in sectionConfig.secondary_menu"><a :href="m.url">{{m.title}}</a></li>
            </ul>
        </div>
    </div>
    <div class="header__inner clearfix">
        <div class="header__info pc clearfix">
            <p class="logo"><a :href="sectionConfig.logo_link">
                <img src="<?php echo APP_ASSETS; ?>img/common/header/logo.svg" :alt="sectionConfig.alternative_logo_text" width="111">
                <span class="logo__txt01">{{sectionConfig.local_office_name}}</span>
                <span class="logo__txt02">{{sectionConfig.main_remarks}}</span>
            </a>
            </p>
            <div class="header__contact">
                <p class="header__tel">Sydney & Melbourne counter service is currently closed.<br />Please email us your enquiry instead!
				<?php /*{{sectionConfig.phone_list.main_phone.label}}<span class="num">{{sectionConfig.phone_list.main_phone.num}}</span> */ ?> </p>
                <div class="header__shop">
                    <p class="shop__btn"><span>{{sectionConfig.labels.more_shops}}</span></p>
                    <div class="shop__tel">
                        <dl v-for="p in sectionConfig.phone_list.more_phones">
                            <dt>{{p.label}}</dt>
                            <dd>{{p.num}}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="header__info02 clearfix sp">
            <p class="logo"><a :href="sectionConfig.logo_link">
                <img src="<?php echo APP_ASSETS; ?>img/common/header/logo_sp.svg" :alt="sectionConfig.alternative_logo_text" width="111">
                <span class="logo__txt01">{{sectionConfig.local_office_name}}</span>
            </a>
            </p>
            <ul class="header__ul03">
                <li class="sub"><a class="shop__btn02" href="javascript:void(0);">{{sectionConfig.labels.call_us}}</a>
                    <ul class="submenu ">
                        <li v-for="p in sectionConfig.phone_list.more_phones"><a :href="'tel:' + p.call_number"><img src="<?php echo APP_ASSETS; ?>img/common/header/ico_tel.svg" alt="" width="17">{{p.label}}</a></li>
                    </ul>
                </li>
                <li>
                    <a :href="sectionConfig.itinerary_url">
                        <span class="tp-badge-container">
                            <img src="<?php echo APP_ASSETS; ?>img/common/header/ico_cart02.svg" width="23" alt="">
                            <span v-if="itineraryItemCount > 0" class="tp-badge">{{itineraryItemCount}}</span>
                        </span>
                    </a>
                </li>
                <li>
                    <div class="menu-btn">
                        <div class="burger-icon"></div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div id="navi" class="header__navi clearfix">
        <div class="header__inner clearfix">
            <ul class="header__ul02">
                <li class="pc sub" v-for="m in sectionConfig.main_menu_right_side">
                    <a v-if="helper.isMobile() && m.children.length > 0">{{m.title}}</a>
                    <a v-if="!(helper.isMobile() && m.children.length > 0)" :href="m.url">{{m.title}}</a>
                    <ul class="submenu">
                        <li v-for="subM in m.children"><a :href="subM.url">{{subM.title}}</a></li>
                    </ul>
                </li>

                <?php if (get_option('tp_site_search_enabled')) { ?>
                
                <li class="form">
                    <form role="search" method="get" action="<?php echo esc_url( site_url( '/' ) ); ?>">
                        <input class="txtSearch" type="text" value="<?php echo get_search_query(); ?>" :placeholder="sectionConfig.labels.search" name="s" id="s" >
                    </form>
                </li>

                <?php } ?>

                <li class="pc">
                    <a :href="sectionConfig.itinerary_url">
                        <span class="tp-badge-container">
                            <img src="<?php echo APP_ASSETS; ?>img/common/header/ico_cart.svg" alt="Cart" width="21" >
                            <span v-if="itineraryItemCount > 0" class="tp-badge">{{itineraryItemCount}}</span>
                        </span>
                    </a>
                </li>
            </ul>
            <p class="logo02"><a :href="sectionConfig.logo_link">
                <img src="<?php echo APP_ASSETS; ?>img/common/header/logo_sp.svg" :alt="sectionConfig.alternative_logo_text" width="111">
            </a>
            </p>
            <ul class="header__ul01 clearfix">
                <li v-for="(m,idx) in sectionConfig.main_menu" :class="{
                act: matchUrl( m.url, sectionConfig.request_url ), 
                sub: !_.isEmpty( m.children )}">
                    <a v-if="helper.isMobile() && m.children.length > 0" v-html="m.title"></a>
                    <a v-if="!(helper.isMobile() && m.children.length > 0)" :href="m.url" v-html="m.title"></a>
                    <ul v-if="!_.isEmpty( m.children )" class="submenu clearfix">
                        <li v-for="subM in m.children" >
                            <a :href="subM.url" v-html="subM.title">></a>
                        </li>
                    </ul>
                </li>               
            </ul>
            <ul class="header__ul04 sp">
                <li v-for="m in sectionConfig.mobile_menu_bottom"><a :href="m.url">{{m.title}}</a></li>
            </ul>
        </div>
    </div>
</header>