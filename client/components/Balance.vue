<template>
    <div class="dropdown">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-wallet-alt"></i>
            {{$t('page.balance')}}
        </button>
        <ul class="dropdown-menu dropdown-menu-end right-0">
            <li v-if="$isSuperAdmin()"><div class="dropdown-item py-2">{{$t('page.total')}}: {{data.reduce((a, b) => a + parseInt(b.balance), 0) | currency}}</div></li>
            <li v-for="(item, index) in data" :key="index">
                <div class="dropdown-item d-flex">
                    <span class="me-3">{{item.account}}</span>
                    <span class="ms-auto">{{item.balance | currency}}</span>
                </div>
            </li>
        </ul>
    </div>
</template>
<script>
import {mapGetters} from "vuex"
export default {
    data() {
        return {

        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
        data: 'data/balance'
    }),
    filters: {
        currency: function (value) {
            let amount = parseInt(value);
            return amount ? amount.toLocaleString() : '';
        }
    },
    mounted() {
        this.$store.dispatch('data/getBalance')
    }
}
</script>
<style lang="scss" scoped>
    .right-0 {
        right: 0 !important;
    }
</style>