
<div class="header_footer" id="wrap" hidden>
    <!-- Main Content ================================================== -->
    <div v-if="dataList.main_slider" class="mainImg">
        <ul class="slider">
        	<li v-if="!helper.isMobile()" v-for="img in dataList.main_slider">
                            <a :href="img.url">
        		<img :src="img.image" alt="" class="pc" :href="img.url">
                            </a>
        	</li>
        	<li v-if="helper.isMobile()" v-for="img in dataList.main_slider">
                            <a :href="img.url">
        		<img :src="img.image" alt="" class="sp" :href="img.url">
                            </a>
        	</li>
        </ul>
    </div>
    <!--main Img-->
    <div class="container">
        <p v-if="dataList.text_data && dataList.text_data.main_remarks" class="welcome">{{dataList.text_data.main_remarks[0].text}}</p>
        <div v-if="dataList.hot_deals" class="section clearfix">
            <h2 class="section__ttl">{{ dataListLabel("hot_deals", "Hot Deals") }}</h2>
            <div class="grid gridHot gridSlider">
                <figure class="effect-sarah" v-for="item in dataList.hot_deals">
                    <a><img :src="item.image" :alt="item.title"></a>
                    <figcaption :href="item.url">
                       <div class="middle">
                            <a :href="item.url">
                                <h3 :href="item.url" v-html="item.title"></h3>
                                <p :href="item.url" v-html="item.description"></p>
                            </a>
                        </div>
                    </figcaption>
                </figure>
            </div>
            <!-- grid-->
        </div>
        <!-- section-->
        <div v-if="dataList.product_categories" class="section clearfix">
            <h2 class="section__ttl">{{dataListLabel("product_categories", "Product Categories")}}</h2>
            <div class="grid gridProduct gridSlider">
                <figure class="effect-marley" v-for="item in dataList.product_categories">
                    <a><img :src="item.image" :alt="item.title"></a>
                    <a :href="item.url">
                        <figcaption>
                            <h3 v-html="item.title" :href="item.url"></h3>
                            <p :href="item.url">{{item.description}}</p>
                        </figcaption>
                    </a>
                </figure>
            </div>
            <!-- grid-->
        </div>
        <!-- section-->
        <div v-if="dataList.japan_information" class="section clearfix">
            <h2 class="section__ttl">{{ dataListLabel("japan_information", "Japan Information") }}</h2>
            <div class="grid gridInfo gridSlider">
                <figure v-for="(n,idx) in dataList.japan_information.image_url_description.length">
                    <a :href="dataList.japan_information.image_url_description[idx].url"><img :src="dataList.japan_information.image_url_description[idx].image.url" :alt="dataList.japan_information.image_url_description[idx].text"></a>
                    <figcaption>
                        <a :href="dataList.japan_information.link1[idx].url" :class="'gridInfo__cat ' + dataList.japan_information.link1_css[idx].text">{{dataList.japan_information.link1[idx].text}}</a>
                        <a :href="dataList.japan_information.link2[idx].url" class="gridInfo__cat">{{dataList.japan_information.link2[idx].text}}</a>
                        <p class="gridInfo__txt">{{dataList.japan_information.image_url_description[idx].text}}</p>
                    </figcaption>
                </figure>
            </div>
        </div>
        <!-- section-->
        <div v-if="dataList.featured_destinations" class="section clearfix">
            <h2 class="section__ttl">{{dataListLabel("featured_destinations", "Featured Destinations")}}</h2>
            <div class="grid gridFeature gridSlider02">
                <figure v-for="item in dataList.featured_destinations">
                    <a :href="item.url"><img :src="item.image" :alt="item.title"></a>
                    <figcaption>
                        <p>{{item.title}}</p>
                    </figcaption>
                </figure>
            </div>
        </div>
        <!-- section-->
        <div v-if="dataList.review_list" class="section section--mb0 clearfix ov-hidden">
            <h2 class="section__ttl">{{dataListLabel("reviews", "Reviews")}}</h2>
            <div class="grid gridReview">
                <div v-for="(n,idx) in dataList.review_list.content.length" class="gridReview__item">
                    <h3 class="gridReview__ttl">{{dataList.review_list.title[idx].text}}</h3>
                    <p class="gridReview__date">{{dataList.review_list.date[idx].text}}</p>
                    <div class="gridReview__content" v-html="dataList.review_list.content[idx].text">
                    </div>
                    <p class="gridReview__link"><a :href="dataList.review_list.url[idx].url">{{dataListLabel("read_more", "Read more")}}</a></p>
                </div>
            </div>
        </div>
        <!-- section-->
        <div v-if="dataList.others" class="section clearfix">
            <h2 class="section__ttl">{{dataListLabel("others", "Others")}}</h2>
            <div class="gridOther">
                <ul class="gridSlider02">
                    <li v-for="item in dataList.others"><a :href="item.url"><img :src="item.image" alt=""></a></li>
                </ul>
            </div>
        </div>
        <!-- section-->
    </div>
    <!--container-->
</div>


<script>
    $(document).ready( function() {
        var rootId = '#wrap';
        // Initialize Vue
        var vueData = new Vue({
            el: rootId,
            mounted: function() {
                $(rootId).removeAttr('hidden');
            },
            methods: {
            },
        });
    });
</script>


<script>
	$(document).ready(function() {
		$(".tp-breadcrumb-container").hide();
	});
</script>