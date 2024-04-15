<template>
    <div id="page-wrapper">
        <header>
            <nav class="navbar navbar-expand-xl navbar-dark dark-skin fbt_sticky_nav">
                <div class="container nav-mobile-px sticky__nav">
                    <div class="navbar-brand mr-auto" id="header-logo" name="Logo">
                        <div class="widget Header" data-version="2" id="Header1">
                            <div class="header-widget">
                                <router-link class="header-image-wrapper" :to="{name: 'index'}">
                                    <img alt="Ejsabet" data-height="56" data-width="300" src="~/assets/images/logo.png" />
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div v-if="allowed_ips.find(i => i.ip_address === clientIp)" class="header-buttons">
                        <router-link v-if="auth_user" :to="{name: auth_transaction}" class="fbt-sidenav ml-1 text-primary">
                            {{ $t('page.transaction') }}
                        </router-link>
                        <router-link v-else :to="{name: 'login'}" class="fbt-sidenav ml-1 text-primary">
                            {{ $t('page.sign_in') }}
                        </router-link>
                    </div>
                </div>
            </nav>
        </header>
        <div class="clearfix"></div>

        <div class="slider-container-row section" id="slider-posts" name="Main Slider">
            <div class="widget FeaturedPost" data-version="2" id="FeaturedPost1">
                <div class="widget-content">
                    <div class="container">
                        <div v-if="banner_post" class="row align-items-center slider-width">
                            <div class="col-lg-7">
                                <div class="fbt-shape-container">
                                    <div class="fbt-item-thumbnail radius-10">
                                        <a class="post-image-link" :href="banner_post.url">
                                            <img
                                                alt="Now that the land is free, it needs time to mourn, and how much."
                                                class="post-thumbnail lazyloaded"
                                                :src="banner_post.image_url"
                                            />
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 mt-4 mt-lg-0">
                                <div class="fbt-shape-title pl-xl-5 pl-lg-4">
                                    <h1 class="display-4">
                                        <a href="javascript:;">
                                            <span v-html="banner_post.title.substring(0, 80)"></span>
                                            <span v-if="banner_post.title.length > 80">...</span>
                                        </a>
                                    </h1>
                                    <div class="post-meta my-4">
                                        <!-- <span class="post-author">
                                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Yuyuan Zhang </font></font>
                                        </span> -->
                                        <span class="post-date published">
                                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ banner_post.published_at }}</font></font>
                                        </span>
                                    </div>
                                    <a :href="banner_post.url">
                                        <span class="fbt_read_more btn btn-primary-slider radius-25 px-5 mt-2">
                                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $t('page.keep_reading') }}...</font></font>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outer Wrapper -->
        <div class="outer-wrapper clearfix" id="outer-wrapper">
            <div class="container fbt-elastic-container">
                <div class="row justify-content-center">
                    <!-- Main Wrapper -->
                    <div class="fbt-main-wrapper col-xl-12">
                        <div id="main-wrapper">
                            <div class="main section" id="main_content" name="Main Content">
                                <div class="widget Blog" data-version="2" id="Blog1">
                                    <div class="row justify-content-center"></div>

                                    <div class="blog-posts hfeed fbt-index-post-wrap card-columns">
                                        <post v-for="(item, index) in data" :key="index" :post="item"></post>
                                    </div>

                                    <div class="blog-pager" id="blog-pager">
                                        <div class="list-inline">
                                            <a class="blog-pager-older-link list-inline-item" @click="getMorePost()" style="cursor:pointer;">
                                                <div class="fbt-bp-message text-uppercase font-weight-bold">
                                                    <font>More posts</font>
                                                </div>
                                                <span aria-hidden="true" class="fa fa-angle-down"></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- #main-wrapper -->
                    </div>
                    <!-- .fbt-main-wrapper -->
                </div>
            </div>
        </div>
        <!-- #outer-wrapper -->

        <div class="clearfix"></div>
        <!-- Footer -->
        <div class="fbt-bottom-shape">
            <svg class="fbt-footer-wave-big" preserveAspectRatio="none" version="1.1" viewBox="5 0 1366 222" width="100%">
                <path d="M-2.19,238H1366v-4.27c-67.87-24-146.44-43.08-230.75-53.19-253.33-27.78-293.94,51.64-541.13,29.89C318.08,186.31,289.49,32.92,6.9,11.73c-5.21-.42-10.56-.7-15.9-1V238Z" transform="translate(9.5 -10.22)"></path>
            </svg>
        </div>
        <div class="footer-dark pt-4" id="footer-content">
            <div class="container pb-4">
                <div class="row clearfix">
                    <div class="col-lg-4">
                        <div class="footer-1" id="footer-1" name="Footer 1">
                            <div class="widget Image" data-version="2" id="Image2">
                                <div class="widget-content">
                                    <img
                                        alt=""
                                        id="Image2_img"
                                        src="~/assets/images/logo.png"
                                        width="200"
                                    />

                                    <br />
                                </div>
                            </div>
                            <div class="widget Text" data-version="2" id="Text1">
                                <div class="widget-content">
                                    <p>
                                        Un Blog que Abraza el Mundo: Noticias Locales, Impacto Global.
                                        <br /><br />
                                        Descubre un abanico de noticias desde la comodidad de un solo lugar. Desde lo local hasta lo global, nuestro blog te ofrece una visión amplia y variada de los eventos más relevantes del mundo. ¡No te pierdas ni un solo titular! Únete a nosotros y mantente al día con las noticias que importan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 last"></div>
                    <div class="col-lg-2 ml-lg-auto">
                        <div class="footer-2" id="footer-2" name="Footer 2">
                            <div class="widget Label" data-version="2" id="Label2">
                                <div class="fbt-sep-title">
                                    <h4 class="title title-heading-left">
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">
                                                About
                                            </font>
                                        </font>
                                    </h4>
                                    <div class="title-sep-container"><div class="title-sep sep-double"></div></div>
                                </div>
                                <div class="widget-content list-label-widget-content">
                                    <ul class="list-unstyled">
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.home') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.lifestyle') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.people') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.slider') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.sport') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="footer-3" id="footer-3" name="Footer 3">
                            <div class="widget Label" data-version="2" id="Label1">
                                <div class="fbt-sep-title">
                                    <h4 class="title title-heading-left">
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">
                                                {{ $t('page.categories') }}
                                            </font>
                                        </font>
                                    </h4>
                                    <div class="title-sep-container"><div class="title-sep sep-double"></div></div>
                                </div>
                                <div class="widget-content list-label-widget-content">
                                    <ul class="list-unstyled">
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.business') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.design') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.lifestyle') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.technology') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.training') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 last">
                        <div class="footer-4" id="footer-4" name="Footer 4">
                            <div class="widget Label" data-version="2" id="Label5">
                                <div class="fbt-sep-title">
                                    <h4 class="title title-heading-left">
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">
                                                {{ $t('page.account') }}
                                            </font>
                                        </font>
                                    </h4>
                                    <div class="title-sep-container"><div class="title-sep sep-double"></div></div>
                                </div>
                                <div class="widget-content list-label-widget-content">
                                    <ul class="list-unstyled">
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.business') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.design') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.entertainment') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.featured') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="label-name" href="javascript:;">
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">
                                                        {{ $t('page.technology') }}
                                                    </font>
                                                </font>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="credits">
                <div class="container">
                    <div class="row divider py-4">
                        <div class="col-lg-6">
                            <div class="copyright-section ">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;"> &copy; </font></font>
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">2024 Todos los derechos reservados a Ejsabet </font></font>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #footer-content -->
    </div>
</template>

<script>
import Post from '~/components/home/Post.vue'
import { mapGetters } from "vuex"
export default {
    layout: "index",
    components: {
        Post,
    },
    head () {
        return { title: this.$t('page.home') }
    },
    data() {
        return {
            page: 1,
            data: [],
            clientIp: '',
        }
    },
    computed: {
        banner_post() {
            return this.data[Math.floor((Math.random() * this.data.length))];
        },
        auth_transaction() {
            if (this.$isAuditor()) {
                return 'audit';
            } else if (this.$isSubAdmin()) {
                return 'daily_transaction';
            } else {
                return 'transaction';
            }
        },
        ...mapGetters({
            auth_user: 'auth/user',
            allowed_ips: 'auth/allowed_ips',
        })
    },
    mounted() {
        $(window).on("scroll", function() {
            var scroll = $(window).scrollTop();
            if (scroll > 300) {
                $(".fbt_sticky_nav").addClass("sticky__nav");
            } else {
                $(".fbt_sticky_nav").removeClass("sticky__nav");
            }
            if (scroll > 400) {
                $(".fbt_sticky_nav").addClass("nav_offset");
            } else {
                $(".fbt_sticky_nav").removeClass("nav_offset");
            }
            if (scroll > 500) {
                $(".fbt_sticky_nav").addClass("scrolling_nav");
            } else {
                $(".fbt_sticky_nav").removeClass("scrolling_nav");
            }
        });
        this.$store.dispatch('auth/fetchAllowedIps');
        this.checkIp();
        this.getPost();
    },
    methods: {
        getPost () {
            this.axios.get('/search_news?page=' + this.page).then(response => {
                let result = this.data.concat(response.data.data.data);
                this.data = result;
            })
        },
        getMorePost() {
            this.page ++;
            this.getPost();
        },
        async checkIp() {
            const response = await this.axios.get('/get_client_ip');
            if (response.data.status === 'Success') {
                this.clientIp = response.data.data.ip;
            }
        }
    }
}
</script>
