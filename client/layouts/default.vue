<template>
    <div id="layout-wrapper">
        <!-- <progress-bar></progress-bar> -->
        <div class="twocolumn-panel" :class="sidebar_collapsed && 'menu'">
            <navbar />
            <sidebar />
            <div class="main-content">
                <div class="container-fluid">
                    <nuxt />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Navbar from '~/components/Navbar'
import Sidebar from '~/components/Sidebar'
import {mapGetters} from "vuex"
import InactivityCheck from '@/mixins/InactivityCheck.js'
export default {
    mixins: [InactivityCheck],
    head() {
        return {
            htmlAttrs: {
                'data-layout': 'horizontal',
                'data-topbar': 'dark',
                'data-layout-mode': this.layout_mode
            },
        };
    },
    components: {
        Navbar, Sidebar
    },
    computed: mapGetters({
        sidebar_collapsed: 'data/sidebar_collapsed',
        layout_mode: 'data/layout_mode',
        allowed_ips: 'auth/allowed_ips',
    }),
    async mounted() {
        await this.$store.dispatch('auth/fetchAllowedIps');
        this.checkIp()
    },
    methods: {
        toggleSidebar() {
            this.$store.dispatch('data/toggleSidebar', false);
        },
        async checkIp() {
            const response = await this.axios.get('/get_client_ip');
            if (response.data.status === 'Success') {
                const clientIp = response.data.data.ip;
                if (this.allowed_ips.length && !this.allowed_ips.find(i => i.ip_address === clientIp)) {
                    return this.$router.push({name: 'index'});
                }
            }
        }
    }
}
</script>
<style src="../assets/sass/app.scss" lang="scss"></style>
