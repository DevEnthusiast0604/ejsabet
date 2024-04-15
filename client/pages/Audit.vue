<template>
    <div class="page-content">
        <div class="d-flex align-items-center mb-3">
            <h4 class="page-title"><i class="bx bx-calendar-check"></i>{{$t('page.auditor')}}</h4>
        </div>
        <div class="row">
            <div class="col-md-12">
                <auditors v-if="$isSuperAdmin()" />
                <div class="card">
                    <div class="card-body p-3">
                        <div class="page-filter d-md-flex">
                            <form action="" class="top-filter" @submit.prevent="searchForm()">
                                <div class="pagesize">
                                    <label class="control-label mb-0">{{$t('page.show')}}: </label>
                                    <select class="form-select" v-model="filter.per_page" @change="searchForm">
                                        <option value="15">15</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                        <option value="500">500</option>
                                        <option value="1000000">All</option>
                                    </select>
                                </div>
                                <select v-if="$isSuperAdmin()" class="form-select fixed-width" v-model="filter.company_id">
                                    <option value="" hidden>{{$t('page.select_company')}}</option>
                                    <option :value="item.id" v-for="(item, index) in companies" :key="index">{{item.name}}</option>
                                </select>
                                <div class="fixed-width">
                                    <date-range-picker
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
                                </div>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i>{{$t('page.search')}}</button>
                                    <button type="button" class="btn btn-danger ms-2" @click="resetFilter()"><i class="bx bx-eraser"></i>{{$t('page.reset')}}</button>
                                </div>
                            </form>
                            <button v-if="auth_user.role == 'auditor' && auth_user.company_id" type="button" class="btn btn-primary mb-2 ms-auto" @click="add()"><i class="bx bx-plus"></i>{{$t('page.add_new')}}</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width:40px;">#</th>
                                        <th scope="col">{{$t('page.user')}}</th>
                                        <th scope="col">{{$t('page.company')}}</th>
                                        <th scope="col">{{$t('page.date')}}</th>
                                        <th scope="col">{{$t('page.balance')}}</th>
                                        <th v-if="editable" scope="col">{{$t('page.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="data.length == 0"><td colspan="15" class="text-center">{{$t('page.no_data')}}</td></tr>
                                    <tr v-for="(item, index) in data" :key="index">
                                        <td>{{ (filter.page - 1) * filter.per_page + index + 1 }}</td>
                                        <td>{{item.user ? item.user.username : ''}}</td>
                                        <td>{{item.company ? item.company.name : ''}}</td>
                                        <td>{{item.date}}</td>
                                        <td>{{item.balance | currency}}</td>
                                        <td v-if="editable" class="py-2">
                                            <a href="#" class="btn-edit" @click.prevent="edit(item)"><i class="bx bx-edit"></i></a>
                                            <a href="#" class="btn-delete" @click.prevent="remove(item, index)"><i class="bx bx-trash"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div>
                                    <p>{{$t('page.total')}} <strong style="color: red">{{ total }}</strong> {{$t('page.items')}}</p>
                                </div>
                                <div>
                                    <paginate
                                        v-model="filter.page"
                                        :page-count="page_count"
                                        :page-range="3"
                                        :margin-pages="2"
                                        :prev-class="'page-item'"
                                        :next-class="'page-item'"
                                        :prev-link-class="'page-link'"
                                        :next-link-class="'page-link'"
                                        :click-handler="searchData"
                                        :container-class="'pagination'"
                                        :page-class="'page-item'"
                                        :prev-text="'<'"
                                        :next-text="'>'"
                                        :page-link-class="'page-link'">
                                    </paginate>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <modal
            name="audit_modal"
            width="500"
            :scrollable="true"
            :adaptive="true"
        >
            <div class="p-2">
                <div class="card mb-0">
                    <div class="card-body">
                        <form class="row g-3" action="" method="post" @submit.prevent="submit()">
                            <h4 class="modal-title">{{$t(`page.${form_mode}`)}} {{$t('page.auditor')}}</h4>
                            <button type="button" class="btn-close" @click="$modal.hide('audit_modal')"></button>
                            <div class="col-12 mb-4">
                                <label class="control-label">{{$t('page.date')}}</label>
                                <datepicker
                                    v-model="form.date"
                                    input-class="form-control mt-1"
                                    format="yyyy-MM-dd"
                                    :use-utc="true"
                                    :typeable="true"
                                    :placeholder="$t('page.date')"
                                    @selected="getBalance"
                                ></datepicker>
                                <has-error :form="form" field="date" />
                            </div>
                            <div class="col-12 mb-4">
                                <label class="control-label">{{$t('page.balance')}}</label>
                                <input class="form-control mt-1" type="text" :value="form.balance | currency" readonly :placeholder="$t('page.balance')" />
                            </div>
                            <div class="col-12 text-right mb-5">
                                <hr class="mb-3" />
                                <button type="button" class="btn btn-danger" @click="$modal.hide('audit_modal')"><i class="bx bx-window-close"></i> {{$t('page.close')}}</button>
                                <button type="submit" class="btn btn-primary ml-1" :class="{'btn-loading': form.busy}" :disabled="form.busy || !balance_loaded"><i class="bx bx-save"></i> {{$t('page.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
import Form from "vform"
import Swal from 'sweetalert2'
import {mapGetters} from "vuex"
import Datepicker from 'vuejs-datepicker';
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import Auditors from "~/components/Auditors.vue"
export default {
    name: 'Audit',
    middleware: 'auth',
    head () {
        return { title: this.$t('page.auditor') }
    },
    components: {
        Datepicker,
        DateRangePicker,
        Auditors,
    },
    data() {
        return {
            dateRange: {
                startDate: '',
                endDate: '',
            },
            data: [],
            filter: {
                page: 1,
                company_id: '',
                date: '',
                per_page: 15,
            },
            total: 0,
            page_count: 0,
            form_mode: 'add',
            balance_loaded: false,
            form: new Form({
                id: '',
                company_id: '',
                date: '',
                balance: '',
            }),
        }
    },
    computed: {
        editable() {
            if (this.auth_user && this.$isAuditor() && !this.auth_user.company_id) return false;
            return true;
        },
        ...mapGetters({
            auth_user: 'auth/user',
            companies: 'data/companies',
        })
    },
    filters: {
        currency: function (value) {
            let amount = parseInt(value);
            return amount ? amount.toLocaleString() : '';
        }
    },
    mounted() {
        this.$store.dispatch('data/toggleSidebar', false);
        this.$store.dispatch('data/getCompanies');
        this.searchData();
    },
    methods: {
        searchForm() {
            this.filter.page = 1;
            this.searchData();
        },
        searchData() {
            if (this.dateRange.startDate) {
                this.filter.startDate = this.$moment(this.dateRange.startDate).format('YYYY-MM-DD');
                this.filter.endDate = this.$moment(this.dateRange.endDate).format('YYYY-MM-DD');
            }
            this.axios.post('/audit/search', this.filter).then(response => {
                if(response.data.status == 'Success') {
                    let result = response.data.data
                    this.data = result.data
                    this.page_count = result.last_page
                    this.total = result.total
                }
            }).catch(error => {
                if (error.response.status === 403) {
                    this.$toast.error(error.response.data.message);
                    window.location.reload();
                }
            })
        },
        add() {
            this.form_mode = 'add';
            this.form.company_id = this.auth_user.company_id;
            this.$modal.show('audit_modal');
        },
        edit(item) {
            this.form_mode = 'edit';
            this.form.id = item.id
            this.form.company_id = item.company_id
            this.form.date = item.date
            this.form.balance = item.balance
            this.$modal.show('audit_modal');
        },
        remove(item, index) {
            Swal.fire({
                icon: 'warning',
                title: this.$t('message.are_you_sure'),
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: this.$t('page.ok'),
                cancelButtonText: this.$t('page.cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.axios.get(`/audit/delete/${item.id}`).then(response => {
                        if (response.data.status == 'Success') {
                            this.$toast.success(this.$t('message.deleted_successfully'));
                            this.data.splice(index, 1);
                        }
                    }).catch(error => {
                        if (error.response.status === 403) {
                            this.$toast.error(error.response.data.message);
                            window.location.reload();
                        }
                    })
                }
            })
        },
        resetFilter() {
            this.dateRange = {
                startDate: '',
                endDate: '',
            }
            this.filter = {
                page: 1,
                company_id: this.auth_user.company_id,
                date: '',
                per_page: 15,
            }
        },
        async getBalance() {
            this.balance_loaded = false;
            await this.$nextTick();
            if (!this.form.company_id || !this.form.date) return
            let params = {
                company_id: this.form.company_id,
                date: this.form.date
            }
            try {
                const response = await this.axios.post('/company/get_balance', params)
                if (response.data.status === 'Success') {
                    this.form.balance = response.data.data.balance
                    this.balance_loaded = true;
                }
            } catch (e) {
                if (e.response.data.message != 'no_transaction') throw e;
                this.$toast.error(this.$t('message.no_transaction_on_the_selected_date'));
                this.form.balance = '';
            }
        },
        submit() {
            this.form.post('/audit/save').then(response => {
                if (response.data.status == 'Success') {
                    let message = this.form_mode == 'edit' ? this.$t('message.updated_successfully') : this.$t('message.created_successfully');
                    this.$toast.success(message);
                    this.form.reset();
                    this.$modal.hide('audit_modal')
                    this.searchData();
                }
            }).catch(error => {
                if (error.response.status === 403) {
                    this.$toast.error(error.response.data.message);
                    window.location.reload();
                }
            })
        },
    }
}
</script>
