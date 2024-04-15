<template>
    <div class="page-content">
        <div class="d-flex align-items-center mb-3">
            <h4 class="page-title"><i class="bx bx-user"></i>{{$t('page.user')}}</h4>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="page-filter d-md-flex justify-content-end mb-2">
                            <button type="button" class="btn btn-primary" @click="add()"><i class="bx bx-plus"></i>{{$t('page.add_new')}}</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width:40px;">#</th>
                                        <th scope="col">{{$t('page.username')}}</th>
                                        <th scope="col">{{$t('page.email')}}</th>
                                        <th scope="col">{{$t('page.company')}}</th>
                                        <th scope="col">{{$t('page.role')}}</th>
                                        <th scope="col">{{$t('page.phone_number')}}</th>
                                        <th scope="col">{{$t('page.ip_address')}}</th>
                                        <th scope="col">{{$t('page.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="data.length == 0"><td colspan="15" class="text-center">{{$t('page.no_data')}}</td></tr>
                                    <tr v-for="(item, index) in data" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{item.username}}</td>
                                        <td>{{item.email}}</td>
                                        <td>{{item.company ? item.company.name : ''}}</td>
                                        <td>{{$t(`page.${item.role}`)}}</td>
                                        <td>{{item.phone_number}}</td>
                                        <td>{{item.ip_address}}</td>
                                        <td class="py-2">
                                            <a href="#" class="btn-edit" @click.prevent="edit(item)"><i class="bx bx-edit"></i></a>
                                            <a href="#" class="btn-delete" @click.prevent="remove(item, index)"><i class="bx bx-trash"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <modal
            name="user_modal"
            width="600"
            height="auto"
            :scrollable="true"
            :adaptive="true"
        >
            <div class="p-2" >
                <div class="card card-body">
                    <form action="" class="row g-3" method="post" @submit.prevent="submit()">
                        <h4 class="modal-title">{{$t(`page.${form_mode}`)}} {{$t('page.user')}}</h4>
                        <button type="button" class="btn-close" @click="$modal.hide('user_modal')"></button>
                        <div class="col-12">
                            <hr class="mt-0 mb-3" />
                            <label class="control-label">{{$t('page.username')}}</label>
                            <input class="form-control mt-1" type="text" v-model="form.username" required :placeholder="$t('page.username')" />
                            <has-error :form="form" field="username" />
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{$t('page.email')}}</label>
                            <input class="form-control mt-1" type="email" v-model="form.email" :placeholder="$t('page.email')" />
                            <has-error :form="form" field="email" />
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{$t('page.company')}}</label>
                            <select class="form-select mt-1" v-model="form.company_id" :required="form.role == 'admin' || form.role == 'sub_admin'">
                                <option value="">{{$t('page.select_company')}}</option>
                                <option :value="item.id" v-for="(item, index) in companies" :key="index">{{item.name}}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{$t('page.phone_number')}}</label>
                            <input class="form-control mt-1" type="text" v-model="form.phone_number" :placeholder="$t('page.phone_number')" />
                            <has-error :form="form" field="phone_number" />
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{$t('page.role')}}</label>
                            <select class="form-select mt-1" v-model="form.role" required>
                                <option value="admin">{{$t('page.admin')}}</option>
                                <option value="super_admin">{{$t('page.super_admin')}}</option>
                                <option value="sub_admin">{{$t('page.sub_admin')}}</option>
                                <option value="auditor">{{$t('page.auditor')}}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{$t('page.ip_address')}}</label>
                            <input class="form-control" type="text" v-model="form.ip_address" :placeholder="$t('page.ip_address')" />
                            <has-error :form="form" field="ip_address" />
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{$t('page.google_authenticator')}}</label>
                            <select class="form-control mt-1" v-model="form.enable_google2fa">
                                <option value="1">{{$t('page.active')}}</option>
                                <option value="0">{{$t('page.inactive')}}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{$t('page.password')}}</label>
                            <input class="form-control mt-1" type="password" v-model="form.password" :placeholder="$t('page.password')" />
                            <span class="text-info">{{$t('message.password_rule')}}</span>
                            <has-error :form="form" field="password" />
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{$t('page.password_confirm')}}</label>
                            <input class="form-control mt-1" type="password" v-model="form.password_confirmation" :placeholder="$t('page.confirm_password')" />
                        </div>
                        <div class="text-right mt-3">
                            <hr class="mt-0 mb-3" />
                            <button type="button" class="btn btn-danger" @click="$modal.hide('user_modal')"><i class="bx bx-window-close"></i> {{$t('page.close')}}</button>
                            <button type="submit" class="btn btn-primary ml-1" :class="{'btn-loading': form.busy}" :disabled="form.busy"><i class="bx bx-save"></i> {{$t('page.save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
import Form from "vform"
import Swal from 'sweetalert2'
import {mapGetters} from 'vuex'
export default {
    middleware: 'auth',
    head () {
        return { title: this.$t('page.user') }
    },
    data() {
        return {
            data: [],
            form_mode: 'add',
            form: new Form({
                id: '',
                username: '',
                email: '',
                phone_number: '',
                role: 'admin',
                company_id: '',
                ip_address: '',
                enable_google2fa: 1,
                password: '',
                password_confirmation: ''
            })
        }
    },
    computed: mapGetters({
        companies: 'data/companies'
    }),
    mounted() {
        this.$store.dispatch('data/toggleSidebar', false);
        this.$store.dispatch('data/getCompanies')
        this.searchData();
    },
    methods: {
        searchData() {
            this.axios.get('/user/search').then(response => {
                this.data = response.data.data
            })
        },
        add() {
            this.form.reset();
            this.form_mode = 'add';
            this.$modal.show('user_modal');
        },
        edit(item) {
            this.form_mode = 'edit';
            this.form.id = item.id
            this.form.username = item.username
            this.form.company_id = item.company_id
            this.form.role = item.role
            this.form.email = item.email
            this.form.phone_number = item.phone_number
            this.form.ip_address = item.ip_address;
            this.form.enable_google2fa = item.enable_google2fa;
            this.$modal.show('user_modal');
        },
        submit() {
            let uri = this.form_mode == 'edit' ? '/user/update' : '/user/create';
            this.form.post(uri).then(response => {
                this.searchData();
                this.form.reset()
                this.$modal.hide('user_modal')
                this.$toast.success(this.form_mode == 'edit' ? this.$t('message.updated_successfully') : this.$t('message.created_successfully'));
            })
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
                    this.axios.get(`/user/delete/${item.id}`).then(response => {
                        if (response.data.status == 'Success') {
                            this.$toast.success(this.$t('message.deleted_successfully'));
                            this.data.splice(index, 1);
                        }
                    })
                }
            })
        }
    }
}
</script>
