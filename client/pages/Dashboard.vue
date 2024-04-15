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
                            <h5 class="card-title mb-0 me-auto">{{$t('page.company_incoming')}}</h5>
                            <form action="" class="top-filter" @submit.prevent="getDailyIncome();">
                                <datepicker v-model="dateDay"
                                     input-class="alzex-datepicker"
                                     format="MM/dd/yyyy"
                                     :use-utc="true"
                                     :typeable="true"
                                     :language="lang.es"
                                     :placeholder="$t('page.date')"
                                />
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i>{{$t('page.view')}}</button>
                                </div>
                            </form>
                        </div>
                        <div class="chart-container mt-3" v-if="data.daily">
                            <company-daily-income-chart :companies="data.daily.name" :incoming="data.daily.incoming"></company-daily-income-chart>
                        </div>
                        <div class="text-center" v-else>
                            <v-loading />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="page-filter d-sm-flex align-items-center">
                            <h5 class="card-title mb-0 me-auto">{{$t('page.monthly_revenue')}}</h5>
                            <form action="" class="top-filter" @submit.prevent="getMonthlyRevenue()">
                                <datepicker v-model="dateMonth"
                                     input-class="alzex-datepicker"
                                     format="MMMM yyyy"
                                     :language="lang.es"
                                     minimum-view="month"
                                     maximum-view="year"
                                />
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i>{{$t('page.view')}}</button>
                                </div>
                            </form>
                        </div>
                        <div class="chart-container mt-3" v-if="data.monthly">
                            <company-monthly-revenue-chart :companies="data.monthly.name" :revenue="data.monthly.revenue"></company-monthly-revenue-chart>
                        </div>
                        <div class="text-center mt-3" v-else>
                            <v-loading />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="page-filter d-sm-flex align-items-center">
                            <h5 class="card-title mb-0 me-auto">{{$t('page.compare_revenue')}}</h5>
                            <form action="" class="top-filter" @submit.prevent="getCompareRevenue()">
                                <div class="fixed-width">
                                    <date-range-picker ref="daterangepicker"
                                         v-model="compareRangeLastYear"
                                         opens="left"
                                         :showWeekNumbers="false"
                                         :showDropdowns="false"
                                         :autoApply="false"
                                        :linkedCalendars="true"
                                    >
                                        <div slot="input" class="daterangepicker-placeholder">
                                            <span v-if="compareRangeLastYear.startDate && compareRangeLastYear.endDate">
                                                {{ $moment(compareRangeLastYear.startDate).format('YYYY-MM-DD') }} ~ {{ $moment(compareRangeLastYear.endDate).format('YYYY-MM-DD') }}
                                            </span>
                                            <span v-else class="text-muted">{{ $t('page.date') }}</span>
                                        </div>
                                    </date-range-picker>
                                </div>
                                <div class="mx-2">vs</div>
                                <div class="fixed-width">
                                    <date-range-picker ref="daterangepicker"
                                         v-model="compareRangeCurrentYear"
                                         opens="left"
                                         :showWeekNumbers="false"
                                         :showDropdowns="false"
                                         :autoApply="false"
                                        :linkedCalendars="true"
                                    >
                                        <div slot="input" class="daterangepicker-placeholder">
                                            <span v-if="compareRangeCurrentYear.startDate && compareRangeCurrentYear.endDate">
                                                {{ $moment(compareRangeCurrentYear.startDate).format('YYYY-MM-DD') }} ~ {{ $moment(compareRangeCurrentYear.endDate).format('YYYY-MM-DD') }}
                                            </span>
                                            <span v-else class="text-muted">{{ $t('page.date') }}</span>
                                        </div>
                                    </date-range-picker>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i>{{$t('page.view')}}</button>
                            </form>
                        </div>
                        <div v-if="data.compare" class="chart-container mt-3">
                            <company-compare-incoming-chart :companies="data.compare.name" :current="data.compare.incoming" :last="data.compare.incoming_last" :rate="data.compare.rate"></company-compare-incoming-chart>
                        </div>
                        <div v-else class="text-center">
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

import CompanyDailyIncomeChart from '~/components/chart/CompanyDailyIncomeChart.vue'
import CompanyMonthlyRevenueChart from '~/components/chart/CompanyMonthlyRevenueChart.vue'
import CompanyCompareIncomingChart from '~/components/chart/CompanyCompareIncomingChart.vue'

import Datepicker from 'vuejs-datepicker';
import {en, es} from 'vuejs-datepicker/dist/locale';

import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';

import {mapGetters} from "vuex"
export default {
    middleware: ['auth'],
    components: {
        Balance,
        Datepicker,
        CompanyDailyIncomeChart,
        CompanyMonthlyRevenueChart,
        CompanyCompareIncomingChart,
        DateRangePicker,
    },
    head () {
        return { title: this.$t('page.home') }
    },
    data() {
        return {
            dateDay: this.$moment().subtract(1, 'days').format('YYYY-MM-DD'),
            dateMonth: this.$moment().format('YYYY-MM'),
            compareRangeLastYear: {
                startDate: this.$moment().clone().subtract(1, 'years').startOf('month').format('YYYY-MM-DD'),
                endDate: this.$moment().clone().subtract(1, 'years').endOf('month').format('YYYY-MM-DD'),
            },
            compareRangeCurrentYear: {
                startDate: this.$moment().clone().startOf('month').format('YYYY-MM-DD'),
                endDate: this.$moment().clone().endOf('month').format('YYYY-MM-DD'),
            },
            lang: {
              "en": en,
              "es": es
            },
            data: {
              daily: null, monthly: null, compare: null
            }
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
        if (this.$isAdmin()) {
            return this.$router.push({name: 'transaction'});
        }
        this.$store.dispatch('data/toggleSidebar', false);
        this.getDailyIncome();
        this.getMonthlyRevenue();
        this.getCompareRevenue();

    },
    methods: {
        getDailyIncome() {
          this.axios.post('/get_daily_income', {"date": this.dateDay}).then(response => {
            this.data.daily = response.data.data;
          })
        },
        getMonthlyRevenue() {
          this.axios.post('/get_monthly_revenue', {"date": this.dateMonth}).then(response => {
            this.data.monthly = response.data.data;
          })
        },
        getCompareRevenue() {
            const payload = {
                start_date_last: this.compareRangeLastYear.startDate,
                end_date_last: this.compareRangeLastYear.endDate,
                start_date_current: this.compareRangeCurrentYear.startDate,
                end_date_current: this.compareRangeCurrentYear.endDate,
            };
            this.axios.post('/get_compare_revenue', payload).then(response => {
                this.data.compare = response.data.data;
            })
        }
    }
}
</script>
