<template>
    <div class="page-content">
        <div class="d-flex align-items-center mb-3">
            <h4 class="page-title"><i class="bx bx-trash"></i>{{$t('page.advanced_delete')}}</h4>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-body">
                    <form action="" method="post" @submit.prevent="requestDelete">
                        <div class="mb-2 datepicker">
                            <label class="control-label mb-1">{{$t('page.date')}}</label>
                            <date-range-picker
                                style="width: 100%;"
                                ref="daterangepicker"
                                opens="right"
                                :showWeekNumbers="false"
                                :showDropdowns="false"
                                :autoApply="false"
                                v-model="dateRange"
                                :linkedCalendars = "true"
                            >
                                <div slot="input" class="daterangepicker-placeholder">
                                    <span v-if="dateRange.startDate && dateRange.endDate">{{ $moment(dateRange.startDate).format('YYYY-MM-DD') }} ~ {{ $moment(dateRange.endDate).format('YYYY-MM-DD') }}</span>
                                    <span class="text-muted" v-else>{{$t('page.date')}}</span>
                                </div>
                            </date-range-picker>
                            <has-error :form="form" field="startDate" />
                        </div>
                        <div class="mb-2">
                            <label class="control-label mb-1">{{$t('page.company')}}:</label>
                            <select class="form-select" v-model="form.company_id">
                                <option value="" hidden>{{$t('page.select_company')}}</option>
                                <option :value="item.id" v-for="(item, index) in companies" :key="index">{{item.name}}</option>
                            </select>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary" :class="{'btn-loading': this.form.busy}" :disabled="this.form.busy || this.show_verify_form">{{$t('page.request')}}</button>
                        </div>
                    </form>
                    <hr class="my-3" v-if="show_verify_form" />
                    <form action="" class="verify" v-if="show_verify_form" @submit.prevent="verify">
                        <div class="mb-3">
                            <label class="control-label mb-2">{{$t('page.verification_code')}}</label>
                            <input type="text" class="form-control" v-model="verify_form.code" required :placeholder="$t('page.verification_code')" />
                            <has-error :form="verify_form" field="code" />
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" :class="{'btn-loading': this.verify_form.busy}">{{$t('page.verify')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Form from 'vform'
import {mapGetters} from "vuex"
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import Swal from "sweetalert2";
export default {
    middleware: 'auth',
    components: {
        DateRangePicker,
    },
    data() {
        return {
            dateRange: {
                startDate: '',
                endDate: '',
            },
            selected_users: null,
            form: new Form({
                startDate: '',
                endDate: '',
                company_id: '',
            }),
            verify_form: new Form({
                code: '',
            }),
            show_verify_form: false,
        }
    },
    computed: mapGetters({
        companies: 'data/companies',
        auth_user: 'auth/user',
    }),
    mounted() {
        this.$store.dispatch('data/getCompanies');
        this.$store.dispatch('data/toggleSidebar', false);
    },
    methods: {
        async requestDelete() {
            if (this.dateRange.startDate && this.dateRange.endDate) {
                this.form.startDate = this.$moment(this.dateRange.startDate).format('YYYY-MM-DD');
                this.form.endDate = this.$moment(this.dateRange.endDate).format('YYYY-MM-DD');
            }
            let uri = '/advanced_delete/request';
            const response = await this.form.post(uri)
            if (response.data.status == 'Success') {
                let result = response.data.data;
                if (this.$isSuperAdmin()) {
                    Swal.fire({
                        icon: 'info',
                        title: this.$t('page.verification_code'),
                        text: result.verification_code,
                    })
                }
                this.show_verify_form = true;
            }
        },
        verify() {
            this.verify_form.post('/advanced_delete/verify').then(response => {
                if(response.data.status == 'Success') {
                    this.$toast.success(this.$t('message.successfully_done'));
                    this.reset();
                }
            });
        },
        reset() {
            this.form.reset();
            this.verify_form.reset()
            this.selected_users = null;
            this.show_verify_form = false;
            this.dateRange = {
                startDate: '',
                endDate: '',
            };
        }
    }
}
</script>
<style lang="scss" scoped>
    .datepicker::v-deep {
        .reportrange-text {
            padding: 10px 16px !important;
            background: transparent;
        }
    }
    [data-layout-mode=dark] {
        .datepicker::v-deep {
            .reportrange-text {
                border-color: #2a2f34;
            }
        }
    }
</style>