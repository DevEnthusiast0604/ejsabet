export default {
    data() {
        return {
            inactivityTimeout: null,
            inactivityTimeLimit: 10 * 60 * 1000 // 10 minutes in milliseconds
        };
    },
    methods: {
        resetInactivityTimer() {
            if (this.inactivityTimeout) {
                clearTimeout(this.inactivityTimeout);
            }
            this.inactivityTimeout = setTimeout(this.logout, this.inactivityTimeLimit);
        },
        async logout() {
            clearTimeout(this.inactivityTimeout);

            await this.$store.dispatch("auth/logout");

            if (this.$route.name != 'index') {
                this.$router.push({name: 'index'});
            }
        }
    },
    mounted() {
        const events = ['mousemove', 'keydown', 'click', 'scroll'];
        events.forEach(event => {
            window.addEventListener(event, this.resetInactivityTimer);
        });
        this.resetInactivityTimer();
    },
    beforeDestroy() {
        const events = ['mousemove', 'keydown', 'click', 'scroll'];
        events.forEach(event => {
            window.removeEventListener(event, this.resetInactivityTimer);
        });
        if (this.inactivityTimeout) {
            clearTimeout(this.inactivityTimeout);
        }
    }
};
