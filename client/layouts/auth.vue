<template>
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
		<div class="auth-page-content overflow-hidden pt-lg-5">
            <nuxt />
        </div>

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                {{new Date().getFullYear()}} Ejsabet. Crafted with <i class="mdi mdi-heart text-danger"></i> by Yuyuan
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
<script>
export default {
    name: 'AuthLayout',
    middleware: "guest",
    computed: {
        allowed_ips() {
            return this.$store.getters['auth/allowed_ips'];
        },
    },
    async mounted() {
        await this.$store.dispatch('auth/fetchAllowedIps');
        this.checkIp()
    },
    methods: {
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