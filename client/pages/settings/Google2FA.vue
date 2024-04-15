<template>
    <div class="card">
        <div class="card-header">{{$t('page.google_authenticator')}}</div>
        <div class="card-body">
            <div class="qr-container" v-if="QR_Code">
                <div v-html="QR_Code" class="qrcode"></div>
            </div>
            <button
                type="button"
                class="btn btn-primary mt-2"
                style="margin-left: 17px;"
                :disabled="!auth_user.enable_google2fa && auth_user.google2fa_secret"
                @click="getQrCode(true)"
            >{{$t('page.generate_qr_code')}}</button>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    export default {
        middleware: 'auth',
        scrollToTop: false,

        data: () => ({
            QR_Code: null
        }),

        computed: mapGetters({
            auth_user: 'auth/user'
        }),

        mounted() {
            if (this.auth_user.enable_google2fa) {
                this.getQrCode(false);
            }
        },

        methods: {
            getQrCode(regenerate) {
                this.axios.post('/settings/generate_2fa_code', {regenerate: regenerate}).then(response => {
                    this.QR_Code = response.data.data
                });
            }
        },
    };
</script>
