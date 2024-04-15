<template>
    <div class="page-content">
        <div class="d-flex align-items-center mb-3">
            <h4 class="page-title"><i class="bx bx-sitemap"></i>{{$t('page.category')}}</h4>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="page-filter d-md-flex">
                            <form action="" class="top-filter" @submit.prevent="searchForm()" v-if="$isSuperAdmin()">
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
                                <select class="form-select fixed-width" v-model="filter.company_id" v-if="$isSuperAdmin()">
                                    <option value="" hidden>{{$t('page.select_company')}}</option>
                                    <option :value="item.id" v-for="(item, index) in companies" :key="index">{{item.name}}</option>
                                </select>
                                <select class="form-select fixed-width" v-model="filter.user_id" v-if="$isSuperAdmin()">
                                    <option value="" hidden>{{$t('page.select_user')}}</option>
                                    <option :value="item.id" v-for="(item, index) in users" :key="index">{{item.username}}</option>
                                </select>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i>{{$t('page.search')}}</button>
                                    <button type="button" class="btn btn-danger ms-2" @click="resetFilter()"><i class="bx bx-eraser"></i>{{$t('page.reset')}}</button>
                                </div>
                            </form>
                            <button type="button" class="btn btn-primary mb-2 ms-auto" @click="add()" v-if="$isAdmin()"><i class="bx bx-plus"></i>{{$t('page.add_new')}}</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width:40px;">#</th>
                                        <th scope="col">{{$t('page.name')}}</th>
                                        <th scope="col">{{$t('page.user')}}</th>
                                        <th scope="col">{{$t('page.type')}}</th>
                                        <th scope="col">{{$t('page.withdraw_from')}}</th>
                                        <th scope="col">{{$t('page.target_account')}}</th>
                                        <th scope="col">{{$t('page.comment')}}</th>
                                        <th scope="col">{{$t('page.status')}}</th>
                                        <th scope="col">{{$t('page.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="data.length == 0"><td colspan="15" class="text-center">{{$t('page.no_data')}}</td></tr>
                                    <tr v-for="(item, index) in data" :key="index">
                                        <td>{{ (filter.page - 1) * filter.per_page + index + 1 }}</td>
                                        <td>{{item.name}}</td>
                                        <td>{{item.user ? item.user.username : ''}}</td>
                                        <td :class="type_class[item.type]">{{$t(`page.${item.type}`)}}</td>
                                        <td>{{item.from_account ? item.from_account.name : ''}}</td>
                                        <td>{{item.to_account ? item.to_account.name : ''}}</td>
                                        <td>{{item.comment}}</td>
                                        <td :class="{
                                            'text-success': item.status == 'active',
                                            'text-info': item.status == 'inactive',
                                            'text-danger': item.status == 'disabled',
                                        }">
                                            {{$t(`page.${item.status}`)}}
                                        </td>
                                        <td class="py-2">
                                            <a v-if="$isSuperAdmin()" href="javascript:;" class="btn-edit" @click.prevent="approveSuperAdmin(item, index)" :title="$t('page.approve')">
                                                <i :class="item.must_be_approved_by_super_admin ? 'bx bxs-check-square' : 'bx bx-check-square'"></i>
                                            </a>
                                            <a v-if="$isSuperAdmin()" href="javascript:;" class="btn-edit" @click.prevent="changeStatus(item)" :title="$t('page.change_status')"><i class="bx bx-power-off"></i></a>
                                            <a v-if="item.status != 'disabled'" href="javascript:;" class="btn-edit" @click.prevent="edit(item)" :title="$t('page.edit')"><i class="bx bx-edit"></i></a>
                                            <a v-if="item.status != 'disabled'" href="javascript:;" class="btn-delete" @click.prevent="remove(item, index)" :title="$t('page.delete')"><i class="bx bx-trash"></i></a>
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
        <modal name="category_modal"
            width="600"
            height="auto"
            :scrollable="true"
            :adaptive="true"
        >
            <div class="p-3">
                <category-form :mode="form_mode" :category="selected_category"></category-form>
            </div>
        </modal>
        <modal name="category_status_modal"
            width="500"
            height="auto"
            :scrollable="true"
            :adaptive="true"
        >
            <div class="p-3">
                <form class="row g-3" action="" method="post" @submit.prevent="submitStatus()">
                    <h4 class="modal-title">{{$t('page.change_status')}}</h4>
                    <button type="button" class="btn-close" @click="$modal.hide('category_status_modal')"></button>
                    <div class="col-12">
                        <label class="control-label">{{$t('page.status')}}</label>
                        <select class="form-select mt-1" v-model="status_form.status">
                            <option value="" hidden>{{$t('page.status')}}</option>
                            <option value="active">{{$t('page.active')}}</option>
                            <option value="inactive">{{$t('page.inactive')}}</option>
                            <option value="disabled">{{$t('page.disabled')}}</option>
                        </select>
                        <has-error :form="status_form" field="status" />
                    </div>
                    <div class="col-12 text-right">
                        <button type="button" class="btn btn-danger" @click="$modal.hide('category_status_modal')"><i class="bx bx-window-close"></i> {{$t('page.close')}}</button>
                        <button type="submit" class="btn btn-primary ml-1" :class="{'btn-loading': status_form.busy}" :disabled="status_form.busy"><i class="bx bx-save"></i> {{$t('page.save')}}</button>
                    </div>
                </form>
            </div>
        </modal>
    </div>
</template>

<script>
import Form from "vform"
import Swal from 'sweetalert2'
import {mapGetters} from "vuex"
import CategoryForm from "~/components/category/CategoryForm.vue"
export default {
    name: 'Category',
    middleware: 'auth',
    components: {
        CategoryForm
    },
    head () {
        return { title: this.$t('page.category') }
    },
    data() {
        return {
            data: [],
            filter: {
                page: 1,
                user_id: '',
                company_id: '',
                keyword: '',
                per_page: 15,
            },
            total: 0,
            page_count: 0,
            form_mode: 'add',
            selected_category: null,
            type_class: {expense: 'text-danger', incoming: 'text-success', transfer: 'text-warning'},
            status_form: new Form({
                id: '',
                status: 'active',
            }),
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
        companies: 'data/companies',
        users: 'data/users',
    }),
    mounted() {
        this.$store.dispatch('data/toggleSidebar', false);
        this.$store.dispatch('data/getCompanies');
        this.$store.dispatch('data/getUsers');
        if (this.$isAdmin()) {
            this.filter.user_id = this.auth_user.id
        }
        this.searchData();
    },
    methods: {
        searchForm() {
            this.filter.page = 1;
            this.searchData();
        },
        searchData() {
            this.axios.post('/category/search', this.filter).then(response => {
                if(response.data.status == 'Success') {
                    let result = response.data.data
                    this.data = result.data
                    this.page_count = result.last_page
                    this.total = result.total
                }
            })
        },
        add() {
            this.form_mode = 'add';
            this.$modal.show('category_modal');
        },
        edit(item) {
            this.form_mode = 'edit';
            this.selected_category = item
            this.$modal.show('category_modal');
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
                    this.axios.delete(`/category/delete/${item.id}`).then(response => {
                        if (response.data.status == 'Success') {
                            this.$toast.success(this.$t('message.deleted_successfully'));
                            this.data.splice(index, 1);
                        }
                    }).catch(error => {
                        if (error.response && error.response.status == 400) {
                            this.$toast.error(error.response.data.message);
                        }
                    });
                }
            });
        },
        approveSuperAdmin(item, index) {
            Swal.fire({
                icon: 'warning',
                title: this.$t('message.are_you_sure'),
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: this.$t('page.ok'),
                cancelButtonText: this.$t('page.cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.axios.post(`/category/approve_by_super_admin/${item.id}`).then(response => {
                        if (response.data.status == 'Success') {
                            this.$toast.success(this.$t('message.successfully_done'));
                            item.must_be_approved_by_super_admin = !item.must_be_approved_by_super_admin;
                        }
                    })
                }
            });
        },
        changeStatus(item) {
            this.status_form.id = item.id;
            this.status_form.status = item.status;
            this.$modal.show('category_status_modal');
        },
        async submitStatus() {
            const response = await this.status_form.post('/category/change_status');
            if (response.data.status === 'Success') {
                this.$toast.success(this.$t('message.successfully_done'));
                this.data.find(i => i.id === this.status_form.id).status = this.status_form.status;
                this.$modal.hide('category_status_modal');
                this.status_form.reset();
            }
        },
        resetFilter() {
            this.filter = {
                page: 1,
                user_id: '',
                company_id: '',
                keyword: '',
                per_page: 15,
            }
        }
    }
}
</script>
