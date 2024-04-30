<template>
    <div class="page-content">
        <vue-topprogress ref="topProgress"></vue-topprogress>
        <div class="d-md-flex align-items-center mb-3">
            <h4 class="page-title mb-2 mb-md-0"><i class="bx bx-notepad"></i>{{ pageTitle }}</h4>
            <div v-if="!$isSubAdmin()" class="ms-auto d-flex justify-content-end">
                <button class="btn btn-primary me-2" :class="{ 'btn-loading': is_organizing }" :disabled="is_organizing"
                    @click="organize()">
                    <i class="bx bx-loader-alt"></i>
                    {{ $t('page.organize') }}
                </button>
                <balance />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="page-filter d-md-flex align-items-center">
                            <form action="" class="top-filter" @submit.prevent="searchForm()">
                                <div class="pagesize">
                                    <label class="control-label mb-0">{{ $t('page.show') }}: </label>
                                    <select class="form-select" v-model="filter.per_page" @change="searchForm">
                                        <option value="5">5</option>
                                        <option value="15">15</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                        <option value="500">500</option>
                                        <option value="1000000">All</option>
                                    </select>
                                </div>
                                <select class="form-select fixed-width" v-model="filter.type">
                                    <option value="" hidden>{{ $t('page.select_type') }}</option>
                                    <option value="expense">{{ $t('page.expense') }}</option>
                                    <option value="incoming">{{ $t('page.incoming') }}</option>
                                    <option value="transfer">{{ $t('page.transfer') }}</option>
                                </select>
                                <select v-if="$isSuperAdmin()" class="form-select fixed-width"
                                    v-model="filter.company_id">
                                    <option value="" hidden>{{ $t('page.select_company') }}</option>
                                    <option v-for="(item, index) in companies" :key="index" :value="item.id">
                                        {{ item.name }}
                                    </option>
                                </select>
                                <div class="fixed-width multiselect-container">
                                    <multiselect v-model="selected_category" :options="categories" :multiple="false"
                                        :preserve-search="true" :placeholder="$t('page.select_category')"
                                        label="name_with_company" track-by="id" :show-labels="false">
                                        <span slot="noOptions" class="no_option_slot d-none"></span>
                                        <span slot="noResult">{{ $t('message.no_elements_found') }}</span>
                                    </multiselect>
                                </div>
                                <div class="fixed-width multiselect-container">
                                    <multiselect v-model="selected_account" :options="accounts" :multiple="false"
                                        :preserve-search="true" :placeholder="$t('page.select_account')" label="name"
                                        track-by="id" :show-labels="false">
                                        <span slot="noOptions" class="no_option_slot d-none"></span>
                                        <span slot="noResult">{{ $t('message.no_elements_found') }}</span>
                                    </multiselect>
                                </div>
                                <input type="text" class="form-control fixed-width" v-model="filter.keyword"
                                    :placeholder="$t('page.keyword')" />
                                <div class="fixed-width">
                                    <date-range-picker ref="daterangepicker" opens="left" :showWeekNumbers="false"
                                        :showDropdowns="false" :autoApply="false" v-model="dateRange"
                                        :linkedCalendars="true">
                                        <div slot="input" class="daterangepicker-placeholder">
                                            <span v-if="dateRange.startDate && dateRange.endDate">{{
                                                $moment(dateRange.startDate).format('YYYY-MM-DD') }} ~ {{
                                                    $moment(dateRange.endDate).format('YYYY-MM-DD') }}</span>
                                            <span class="text-muted" v-else>{{ $t('page.date') }}</span>
                                        </div>
                                    </date-range-picker>
                                </div>
                                <select v-if="$isSuperAdmin() || $isAdmin()"
                                    class="form-select fixed-width fixed-width-sm"
                                    v-model="filter.is_approved_by_super_admin">
                                    <option value="" hidden>{{ $t('page.approved') }}</option>
                                    <option value="1">{{ $t('page.yes') }}</option>
                                    <option value="0">{{ $t('page.no') }}</option>
                                </select>
                                <select v-if="$isAdmin() || $isSuperAdmin()"
                                    class="form-select fixed-width fixed-width-sm" v-model="filter.is_authorized">
                                    <option value="" hidden>{{ $t('page.authorized') }}</option>
                                    <option value="yes">{{ $t('page.yes') }}</option>
                                    <option value="no">{{ $t('page.no') }}</option>
                                </select>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary mb-2 mb-md-0">
                                        <i class="bx bx-search"></i>{{ $t('page.search') }}
                                    </button>
                                    <button type="button" class="btn btn-danger mb-2 mb-md-0 ms-2"
                                        @click="resetFilter()">
                                        <i class="bx bx-eraser"></i>{{ $t('page.reset') }}
                                    </button>
                                </div>
                            </form>
                            <button v-if="$isAdmin() || $isSubAdmin()" type="button"
                                class="btn btn-primary mb-2 ms-md-auto" @click="add()">
                                <i class="bx bx-plus"></i>
                                {{ $t('page.add_new') }}
                            </button>
                        </div>
                        <div class="text-center my-5" v-if="loading">
                            <v-loading />
                        </div>
                        <div class="table-responsive" v-else>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width:40px;">#</th>
                                        <th scope="col">{{ $t('page.date') }}</th>
                                        <th scope="col">{{ $t('page.company') }}</th>
                                        <th scope="col">{{ $t('page.category') }}</th>
                                        <th scope="col" style="min-width: 270px;flex-direction: column;">{{
                                            $t('page.description') }}</th>
                                        <th scope="col">{{ $t('page.amount') }}</th>
                                        <th scope="col">{{ $t('page.withdraw_from') }}</th>
                                        <th scope="col">{{ $t('page.target_account') }}</th>
                                        <th scope="col">{{ $t('page.user') }}</th>
                                        <th scope="col">{{ $t('page.type') }}</th>
                                        <th scope="col">{{ $t('page.created_at') }}</th>
                                        <th v-if="$isAuditor()" scope="col">
                                            {{ $t('page.audited') }}
                                        </th>
                                        <th v-else scope="col" style="width: 82px;">{{ $t('page.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="data.length == 0">
                                        <td colspan="15" class="text-center">{{ $t('page.no_data') }}</td>
                                    </tr>
                                    <tr v-for="(item, index) in data" :key="index">
                                        <td>{{ (filter.page - 1) * filter.per_page + index + 1 }}</td>
                                        <td>{{ $moment(item.timestamp).format('YYYY-MM-DD') }}</td>
                                        <td>{{ item.company ? item.company.name : '' }}</td>
                                        <td>{{ item.category ? item.category.name : '' }}</td>
                                        <td :class="item.images.length && 'py-1'">
                                            {{ item.description }}
                                            <span class="clearfix" v-viewer v-if="item.images.length">
                                                <img v-for="image in item.images" :key="image.id" :src="image.src"
                                                    class="item-image ms-2 mobile_version" />
                                            </span>
                                        </td>
                                        <td :class="type_class[item.type]">
                                            <span v-if="item.type == 'expense'">-</span>{{ item.amount | currency }}
                                        </td>
                                        <td>{{ item.account ? item.account.name : '' }}</td>
                                        <td>{{ item.target ? item.target.name : '' }}</td>
                                        <td>{{ item.user ? item.user.username : '' }}</td>
                                        <td :class="type_class[item.type]">{{ $t(`page.${item.type}`) }}</td>
                                        <td>{{ $moment(item.created_at).format('HH:mm') }}</td>
                                        <td v-if="$isAuditor()" class="py-2 text-center">
                                            <div
                                                class="form-check form-switch form-switch-audit form-switch-md ms-2 mt-1 text-center">
                                                <input type="checkbox" class="form-check-input"
                                                    :checked="item.is_audited" @click="audit($event, item)">
                                            </div>
                                        </td>
                                        <td v-else class="py-2">
                                            <a v-if="$isSuperAdmin() && item.must_be_approved_by_super_admin"
                                                href="javascript:;" class="btn-edit"
                                                @click.prevent="superAdminApprove(item, index)"
                                                :title="$t('page.approve')">
                                                <i class="bx bx-check-shield"></i>
                                            </a>
                                            <a v-if="item.is_authorizable && $isAdmin()" href="javascript:;"
                                                class="btn-edit" @click.prevent="authorize(item, index)"
                                                :title="$t('page.authorize')">
                                                <i class="bx bx-check-shield"></i>
                                            </a>
                                            <!-- <a v-if="item.is_editable && !item.is_uploading" href="javascript:;" -->
                                            <a v-if="item.is_editable || $isSuperAdmin()" href="javascript:;"
                                                class="btn-edit" @click.prevent="edit(item)">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <!-- <a v-if="item.is_editable && !item.is_uploading" href="javascript:;" -->
                                            <a v-if="item.is_editable || $isSuperAdmin()" href="javascript:;"
                                                class="btn-delete" @click.prevent="remove(item, index)">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="text-danger text-center">
                                    <tr v-if="loadingTotal">
                                        <td colspan="2">{{ $t('page.total') }}</td>
                                        <td colspan="10" class="text-center total-loading">
                                            <v-loading />
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="2">{{ $t('page.total') }}</td>
                                        <td colspan="3" class="text-danger">
                                            {{ $t('page.expenses') }}: -{{ total_expenses | currency }}
                                        </td>
                                        <td colspan="2" class="text-success">
                                            {{ $t('page.incomes') }}: {{ total_incomes | currency }}
                                        </td>
                                        <td colspan="2" class="text-warning" v-for="item in totalBalance" :key="item.id"
                                            v-if="filter.account_id == item.id">
                                            {{ $t('page.transfers_incomes') }}: {{ transfers_incomes | currency }}
                                        </td>
                                        <td colspan="2" class="text-warning" v-for="item in totalBalance" :key="item.id"
                                            v-if="filter.account_id == item.id">
                                            {{ $t('page.transfers_expenses') }}: {{ transfers_expenses | currency }}
                                        </td>
                                        <td colspan="2"
                                            :class="{ 'text-success': item.balance >= 0, 'text-danger': item.balance < 0 }"
                                            v-for="item in totalBalance" :key="item.id"
                                            v-if="filter.account_id == item.id">
                                            {{ $t('page.profit') }}: {{ item.balance | currency }}
                                        </td>
                                        <td colspan="10"
                                            :class="{ 'text-success': total_profit >= 0, 'text-danger': total_profit < 0 }"
                                            v-if="!filter.account_id">
                                            {{ $t('page.profit') }}: {{ total_profit | currency }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div>
                                    <p>{{ $t('page.total') }} <strong style="color: red">{{ total }}</strong>
                                        {{ $t('page.items') }}</p>
                                </div>
                                <div>
                                    <paginate v-model="filter.page" :page-count="page_count" :page-range="3"
                                        :margin-pages="2" :prev-class="'page-item'" :next-class="'page-item'"
                                        :prev-link-class="'page-link'" :next-link-class="'page-link'"
                                        :click-handler="searchData" :container-class="'pagination'"
                                        :page-class="'page-item'" :prev-text="'<'" :next-text="'>'"
                                        :page-link-class="'page-link'">
                                    </paginate>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <modal name="transaction_modal" width="600" height="auto" :scrollable="true" :adaptive="true">
            <div class="p-3">
                <transaction-form :mode="form_mode" :transaction="selected_transaction" @submitted="searchForm"
                    @start-uploading="startUploading" @reloadPage="reloadPage" @updateImage="updateImage"
                    :categoriesForm="categoriesForm" />
            </div>
        </modal>
    </div>
</template>

<script>
import Balance from '~/components/Balance.vue'
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import { mapGetters } from "vuex"
import 'viewerjs/dist/viewer.css'
import { directive as viewer } from "v-viewer"
import Swal from 'sweetalert2'
import TransactionForm from "~/components/transaction/TransactionForm.vue"
import { vueTopprogress } from 'vue-top-progress'
export default {
    name: 'Transaction',
    middleware: 'auth',
    directives: {
        viewer: viewer({
            debug: true,
        }),
    },
    components: {
        Balance,
        DateRangePicker,
        TransactionForm,
        vueTopprogress,
    },
    head() {
        return { title: this.$t('page.transaction') }
    },
    data() {
        return {
            serverUrl: process.env.serverUrl,
            categories: [],
            accounts: [],
            dateRange: {
                startDate: '',
                endDate: '',
            },
            filter: {
                page: 1,
                is_approved_by_super_admin: '',
                company_id: '',
                category_id: '',
                account_id: '',
                is_authorized: '',
                keyword: '',
                type: '',
                per_page: 5,
            },
            total: 0,
            page_count: 0,
            data: [],
            type_class: { expense: 'text-danger', incoming: 'text-success', transfer: 'text-warning' },
            total_expenses: 0,
            total_incomes: 0,
            transfers: 0,
            transfers_incomes: 0,
            transfers_expenses: 0,
            selected_transaction: null,
            loading: true,
            loadingTotal: false,
            form_mode: 'add',
            selected_category: null,
            selected_account: null,
            is_organizing: false,
            flag: false,
            uploadingImage: false,
            categoriesForm: []
        }
    },
    computed: {
        total_profit() {
            return this.total_incomes - this.total_expenses
        },
        pageTitle() {
            if (this.$route.name === 'transaction_approved') return this.$t('page.approved_transactions');
            return this.$t('page.transaction');
        },
        ...mapGetters({
            auth_user: 'auth/user',
            companies: 'data/companies',
            totalBalance: 'data/balance'
        })
    },
    watch: {
        selected_category: function (newCat) {
            this.filter.category_id = newCat ? newCat.id : ''
        },
        selected_account: function (newCat) {
            this.filter.account_id = newCat ? newCat.id : ''
        },
        uploadingImage(value) {
            if (value) {
                window.addEventListener('beforeunload', this.handleBeforeUnload);
            } else {
                window.removeEventListener('beforeunload', this.handleBeforeUnload);
            }
        },
    },
    filters: {
        currency: function (value) {
            let amount = parseInt(value);
            return amount ? amount.toLocaleString() : '';
        }
    },
    mounted() {
        if (this.$isAuditor() && !this.auth_user.company_id) {
            return this.$router.push({ name: 'audit' });
        }
        this.$store.dispatch('data/getCompanies');
        this.getCategories();
        this.getCategoriesForm();
        this.searchData();
        this.$store.dispatch('data/toggleSidebar', false);
        this.getTotal();
        this.initFirebase();
        this.getAccounts();
    },
    beforeDestroy() {
        window.removeEventListener('beforeunload', this.handleBeforeUnload);
    },
    methods: {
        async getCategories() {
            let user_id = this.$isSuperAdmin() ? '' : this.auth_user.id;
            let response = await this.axios.post('/category/search', { user_id })
            this.categories = response.data.data
        },
        async getAccounts() {
            const response = await this.axios.get('/get_accounts')
            this.accounts = response.data.data;
        },
        async getCategoriesForm() {
            const user_id = this.$isAdmin() || this.$isSubAdmin() ? this.auth_user.id : '';
            const response = await this.axios.post('/category/search', { user_id: user_id, from: 'transaction_form' });
            this.categoriesForm = response.data.data;
        },
        initFirebase() {
            if (this.$isAdmin() || this.$isSuperAdmin()) {
                this.$fire.database.ref('transactions/created').remove();
                const createdTransactionsRef = this.$fire.database.ref('transactions/created').limitToLast(1);
                createdTransactionsRef.on('value', (snapshot) => {
                    snapshot.forEach((doc) => {
                        // SubAdmin created a transaction
                        const payload = doc.val();
                        if (payload.user_id != this.auth_user.id) {
                            this.searchForm();
                        }
                    });
                });
            }
        },
        searchForm() {
            this.filter.page = 1
            this.searchData();
            this.getTotal();
        },
        reloadPage() {
            if (this.dateRange.startDate) {
                this.filter.startDate = this.$moment(this.dateRange.startDate).format('YYYY-MM-DD');
                this.filter.endDate = this.$moment(this.dateRange.endDate).format('YYYY-MM-DD');
            }
            this.axios.post('/transaction/search', this.filter).then(response => {
                if (response.data.status == 'Success') {
                    let result = response.data.data;
                    this.data = result.data;
                    this.page_count = result.last_page;
                    this.total = result.total;
                }
            }).catch(error => {
                this.loading = false;
                if (error.response.status === 403) {
                    this.$toast.error(error.response.data.message);
                    // window.location.reload();
                }
            })
        },
        searchData() {
            if (this.dateRange.startDate) {
                this.filter.startDate = this.$moment(this.dateRange.startDate).format('YYYY-MM-DD');
                this.filter.endDate = this.$moment(this.dateRange.endDate).format('YYYY-MM-DD');
            }
            if (this.$route.name === 'transaction_approved') {
                this.filter.is_approved_by_super_admin = 1;
            }
            this.loading = true
            this.axios.post('/transaction/search', this.filter).then(response => {
                this.loading = false
                if (response.data.status == 'Success') {
                    let result = response.data.data;
                    this.data = result.data;
                    this.transfers_expenses = 0;
                    this.transfers_incomes = 0;
                    for (let transaction of result.data) {
                        (transaction.type == "transfer" && transaction.from == this.filter.account_id) && (
                            this.transfers_expenses += transaction.amount
                        );
                        (transaction.type == "transfer" && transaction.to == this.filter.account_id) && (
                            this.transfers_incomes += transaction.amount
                        );
                    }
                    this.page_count = result.last_page;
                    this.total = result.total;
                }
            }).catch(error => {
                this.loading = false;
                if (error.response.status === 403) {
                    if (error.response.data.message) {
                        this.$toast.error(error.response.data.message);
                    }
                    this.$router.push({ name: 'index' });
                }
            })
        },
        async getTotal() {
            if (this.dateRange.startDate) {
                this.filter.startDate = this.$moment(this.dateRange.startDate).format('YYYY-MM-DD');
                this.filter.endDate = this.$moment(this.dateRange.endDate).format('YYYY-MM-DD');
            }
            this.loadingTotal = true
            const response = await this.axios.post('/transaction/get_total', this.filter);
            this.loadingTotal = false;
            if (response.data.status == 'Success') {
                this.total_expenses = Number(response.data.data.expenses)
                this.total_incomes = Number(response.data.data.incomes)
            }
        },
        add() {
            this.selected_transaction = null;
            this.form_mode = 'add',
                this.$modal.show('transaction_modal')
        },
        edit(item) {
            if (item.is_audited) {
                return this.$toast.error(this.$t('message.already_audited'));
            }
            this.form_mode = 'edit',
                this.selected_transaction = item;
            this.$modal.show('transaction_modal');
        },
        remove(item, index) {
            if (item.is_audited) {
                return this.$toast.error(this.$t('message.already_audited'));
            }
            Swal.fire({
                icon: 'warning',
                title: this.$t('message.are_you_sure'),
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: this.$t('page.ok'),
                cancelButtonText: this.$t('page.cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.data.splice(index, 1);
                    this.$toast.success(this.$t('message.deleted_successfully'));
                    this.axios.delete(`/transaction/delete/${item.id}`).then(response => {
                        if (response.data.status == 'Success') {
                            this.$store.dispatch('data/getBalance');
                            this.getTotal();
                        }
                    })
                }
            })
        },
        authorize(item, index) {
            if (!item.is_authorizable) {
                return this.$toast.error(this.$t('message.already_authorized'));
            }
            Swal.fire({
                icon: 'warning',
                title: this.$t('message.are_you_sure_to_authorize'),
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: this.$t('page.ok'),
                cancelButtonText: this.$t('page.cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.axios.post(`/transaction/authorize/${item.id}`).then(response => {
                        if (response.data.status == 'Success') {
                            this.$toast.success(this.$t('message.authorized_successfully'));
                            item.is_authorizable = false;
                            item.authorized_by_id = this.auth_user.id;
                            // Send notification to sub admin
                            const joinData = this.$fire.database.ref(`transactions/${this.auth_user.company_id}/approved`).push();
                            joinData.set({
                                transaction_id: item.id,
                                authorized_by_id: this.auth_user.id,
                            });
                        }
                    })
                }
            })
        },
        superAdminApprove(item, index) {
            if (!this.$isSuperAdmin()) {
                return this.$toast.error(this.$t('message.you_dont_have_permission'));
            }
            Swal.fire({
                icon: 'warning',
                title: this.$t('message.are_you_sure'),
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: this.$t('page.ok'),
                cancelButtonText: this.$t('page.cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.axios.post(`/transaction/super_admin_approve/${item.id}`).then(response => {
                        if (response.data.status == 'Success') {
                            this.$toast.success(this.$t('message.successfully_done'));
                            item.is_approved_by_super_admin = true;
                            item.must_be_approved_by_super_admin = false;
                        }
                    })
                }
            })

        },
        async audit(event, item) {
            try {
                const response = await this.axios.post('/transaction/audit', { id: item.id });
                if (response.data.status === 'Success') {
                    item.is_audited = response.data.data;
                }
            } catch (error) {
                if (error.response.status === 403) {
                    this.$toast.error(error.response.data.message);
                    window.location.reload();
                }
            }
        },
        resetFilter() {
            this.selected_category = null
            this.selected_account = null
            this.dateRange = {
                startDate: '',
                endDate: '',
            }
            this.filter = {
                page: 1,
                company_id: '',
                category_id: '',
                is_authorized: '',
                is_approved_by_super_admin: '',
                keyword: '',
                type: '',
                per_page: 15,
            }
        },
        organize() {
            Swal.fire({
                icon: 'warning',
                title: this.$t('message.are_you_sure'),
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: this.$t('page.ok'),
                cancelButtonText: this.$t('page.cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.is_organizing = true
                    this.axios.post('/transaction/organize').then(response => {
                        if (response.data.status == 'Success') {
                            this.$toast.success(this.$t('message.successfully_done'))
                            this.resetFilter()
                            this.searchData()
                        }
                        this.is_organizing = false
                    }).catch(error => {
                        this.is_organizing = false
                    })
                }
            });
        },
        startUploading(imageForm) {
            if (imageForm.images) {
                this.$refs.topProgress.start()
                this.uploadingImage = true;
                imageForm.post('/transaction/upload_image').then(response => {
                    if (response.data.status === 'Success') {
                        let transaction = this.data.find(i => i.id === response.data.data.id);
                        if (transaction) {
                            transaction.images = response.data.data.images;
                            transaction.disabled = false;
                            transaction.is_uploading = false;
                            // this.jobIds = response.data.job_ids;
                        }
                    }
                    this.axios.post('/transaction/search', this.filter).then(response => {
                        try {
                            if (response.data.status == 'Success') {
                                let result = response.data.data;
                                this.data = result.data;
                                this.page_count = result.last_page;
                                this.total = result.total;
                                this.transfers = 0;
                                for (let transaction of result.data) {
                                    if (transaction.type == "transfer") {
                                        this.transfers += transaction.amount;
                                    }
                                }
                                this.loading = false;

                                imageForm.reset();
                                this.$refs.topProgress.done();
                                this.$toast.success(this.$t('message.updated_successfully'));
                                this.uploadingImage = false;
                            }
                        } catch (error) {
                            this.loading = false;
                            if (error.response && error.response.status === 403) {
                                if (error.response.data.message) {
                                    this.$toast.error(error.response.data.message);
                                }
                                this.$router.push({ name: 'index' });
                            }
                        }
                    })

                    const joinData = this.$fire.database.ref(`transactions/created`).push();
                    joinData.set({
                        transaction_id: response.data.data.id,
                        user_id: this.auth_user.id,
                    });
                }).catch(error => {
                    this.$refs.topProgress.done();
                    this.uploadingImage = false;
                }).finally(() => {
                    this.uploadingImage = false;
                });
            }
        },
        async updateImage(payload) {
            let { form, imageForm } = payload
            await form.post("/transaction/update").then(response => {
                imageForm.id = response.data.data.id
                this.startUploading(imageForm)
            });
        },
        handleBeforeUnload(event) {
            if (this.uploadingImage) {
                const message = this.$t('message.are_you_sure_you_want_to_reload_page');
                event.returnValue = message;
                return message;
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.total-loading {
    &::v-deep {
        .loading-window {
            height: 0;
            width: 250px;
            scale: 0.35;
            margin-top: -15px;
        }
    }
}
</style>
