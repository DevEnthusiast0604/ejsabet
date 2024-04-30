<template>
    <div class="page-content">
        <div class="d-flex align-items-center mb-3">
            <h4 class="page-title"><i class="bx bx-notepad"></i>{{$t('page.add_new_transaction')}}</h4>
        </div>
        <div class="row" v-if="$isAdmin()">
            <div class="col-md-10 col-lg-8">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-primary" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#" :class="{active: type == 1}" @click="type = 1">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-archive-out font-18 me-1"></i></div>
                                        <div class="tab-title">{{$t('page.expense')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#" :class="{active: type == 2}" @click="type = 2">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-archive-in font-18 me-1"></i></div>
                                        <div class="tab-title">{{$t('page.incoming')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#" :class="{active: type == 3}" @click="type = 3">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-exit font-18 me-1"></i></div>
                                        <div class="tab-title">{{$t('page.transfer')}}</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <div class="py-3">
                            <transaction-form mode="add" :type="type"></transaction-form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TransactionForm from "~/components/transaction/TransactionForm.vue"
import {mapGetters} from "vuex"
export default {
    components: {
        TransactionForm
    },
    data() {
        return {
            type: 1,
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user'
    }),
    created() {
        if (this.auth_user.role != 'user') {
            this.$router.back();
        }
    }
}
</script>