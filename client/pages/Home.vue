<template>
    <div class="page-content">
        <div class="d-flex align-items-center mb-3">
            <h4 class="page-title"><i class="bx bx-home"></i>{{$t('page.dashboard')}}</h4>
            <div class="ms-auto">
                <balance />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="page-filter d-sm-flex align-items-center">
                            <h5 class="card-title mb-0 me-auto">{{$t('page.overview')}}</h5>
                            <form action="" class="top-filter" @submit.prevent="getData()">
                                <select class="form-select fixed-width" v-model="filter.company_id" v-if="$isSuperAdmin()">
                                    <option :value="item.id" v-for="(item, index) in companies" :key="index">{{item.name}}</option>
                                </select>
                                <div class="">
                                    <datepicker
                                        v-model="endDate"
                                        input-class="alzex-datepicker"
                                        format="yyyy-MM-dd"
                                        :use-utc="true"
                                        :typeable="true"
                                        :placeholder="$t('page.date')"
                                    ></datepicker>
                                </div>
                                <div class="fixed-width">
                                    <date-range-picker
                                        ref="daterangepicker"
                                        opens="left"
                                        :showWeekNumbers="false"
                                        :showDropdowns="false"
                                        :autoApply="false"
                                        v-model="dateRange"
                                        :linkedCalendars = "true"
                                    ></date-range-picker>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i>{{$t('page.search')}}</button>
                                    <button type="button" class="btn btn-danger ms-2" @click="resetFilter()"><i class="bx bx-eraser"></i>{{$t('page.reset')}}</button>
                                </div>
                            </form>
                        </div>
                        <div class="chart-container mt-3" v-if="data">
                            <overview-chart
                                :keys="data.key_array"
                                :total_expenses="data.total_expense_array"
                                :total_incomings="data.total_incoming_array"
                            ></overview-chart>
                        </div>
                        <div class="text-center mt-3" v-else>
                            <v-loading />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mt-3" v-if="$isSuperAdmin()">
                    <div class="card-body p-3">
                        <h5 class="card-title">{{$t('page.company_balance')}}</h5>
                        <div class="chart-container mt-3" v-if="data">
                            <company-balance-chart
                                :companies="data.company_array"
                                :balance="data.company_balance"
                            ></company-balance-chart>
                        </div>
                        <div class="text-center" v-else>
                            <v-loading />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mt-3" v-if="$isSuperAdmin()">
                    <div class="card-body p-3">
                        <h5 class="card-title">{{$t('page.company_incoming')}}</h5>
                        <div class="chart-container mt-3" v-if="data">
                            <company-incoming-chart
                                :keys="data.key_array"
                                :companies="data.company_array"
                                :incoming="data.company_incoming"
                            ></company-incoming-chart>
                        </div>
                        <div class="text-center" v-else>
                            <v-loading />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mt-3">
                    <div class="card-body p-3">
                        <h5 class="card-title">{{$t('page.user_overview')}}</h5>
                        <div class="chart-container mt-3" v-if="data">
                            <user-overview-chart
                                :users="data.user_array"
                                :expense="data.users_expense"
                                :incoming="data.users_incoming"
                            ></user-overview-chart>
                        </div>
                        <div class="text-center" v-else>
                            <v-loading />
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body p-3">
                        <h5 class="card-title">{{$t('page.account_overview')}}</h5>
                        <div class="chart-container mt-3" v-if="data">
                            <account-overview-chart
                                :accounts="data.account_array"
                                :expense="data.accounts_expense"
                                :incoming="data.accounts_incoming"
                                :balance="data.accounts_balance"
                            ></account-overview-chart>
                        </div>
                        <div class="text-center" v-else>
                            <v-loading />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Balance from '~/components/Balance.vue'
import OverviewChart from '~/components/chart/OverviewChart.vue'
import UserOverviewChart from '~/components/chart/UserOverviewChart.vue'
import AccountOverviewChart from '~/components/chart/AccountOverviewChart.vue'
import CompanyIncomingChart from '~/components/chart/CompanyIncomingChart.vue'
import CompanyBalanceChart from '~/components/chart/CompanyBalanceChart.vue'
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import Datepicker from 'vuejs-datepicker';
import {mapGetters} from "vuex"
export default {
    middleware: ['auth'],
    components: {
        Balance,
        DateRangePicker,
        Datepicker,
        OverviewChart,
        CompanyIncomingChart,
        UserOverviewChart,
        AccountOverviewChart,
        CompanyBalanceChart
    },
    head () {
        return { title: this.$t('page.home') }
    },
    data() {
        return {
            companies: [],
            dateRange: {
                startDate: this.$moment().startOf('month').format('YYYY-MM-DD'),
                endDate: this.$moment().format('YYYY-MM-DD'),
            },
            endDate: '',
            filter: {
                company_id: '',
                startDate: this.$moment().startOf('month').format('YYYY-MM-DD'),
                endDate: this.$moment().format('YYYY-MM-DD'),
            },
            data: null,
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
    }),
    mounted() {
        if (this.$isAuditor()) {
            return this.$router.push({name: 'audit'});
        }
        if (this.$isSubAdmin()) {
            return this.$router.push({name: 'daily_transaction'});
        }
        if (this.$isSuperAdmin()) {
            return this.$router.push({name: 'home.superadmin'});
        }
        this.$store.dispatch('data/toggleSidebar', false);
        this.getCompanies();
    },
    methods: {
        getData() {
            this.filter.startDate = this.$moment(this.dateRange.startDate).format('YYYY-MM-DD');
            this.filter.endDate = this.$moment(this.dateRange.endDate).format('YYYY-MM-DD');
            if (this.endDate) {
                this.filter.startDate = this.$moment(this.endDate).startOf('month').format('YYYY-MM-DD'),
                this.filter.endDate = this.$moment(this.endDate).format('YYYY-MM-DD');
            }
            this.data = null;
            this.axios.post('/get_dashboard_data', this.filter).then(response => {
                this.data = response.data.data;
            })
        },
        getCompanies() {
            this.axios.get('/company/search').then(response => {
                this.companies = response.data.data;
                this.filter.company_id = this.companies[0].id
                if (this.$isAdmin()) {
                    this.filter.company_id = this.auth_user.company_id
                }
                this.getData();
            })
        },
        resetFilter() {
            let company_id = this.companies[0].id
            if (this.$isAdmin()) {
                company_id = this.auth_user.company_id
            }
            this.filter = {
                company_id: company_id,
                startDate: this.$moment().startOf('month').format('YYYY-MM-DD'),
                endDate: this.$moment().format('YYYY-MM-DD'),
            }
            this.endDate = '';
        }
    }
}
</script>
