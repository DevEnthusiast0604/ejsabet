<template>
	<nuxt />
</template>

<script>
import InactivityCheck from '@/mixins/InactivityCheck.js'
export default {
    mixins: [InactivityCheck],
    head() {
        return {
            htmlAttrs: {
                'data-layout-mode': 'index'
            },
        };
    },
}
</script>
<style>
    @import url('https://fonts.googleapis.com/css?family=Nunito:400,700');
</style>
<style src="../assets/sass/bootstrap4.5.2.css" lang="css" scoped></style>
<style src="../assets/sass/index.scss" lang="scss"></style>

