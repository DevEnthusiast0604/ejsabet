<template>
    <div class="page-content">
        <div class="d-flex align-items-center mb-3">
            <h4 class="page-title"><i class="bx bx-grid-alt"></i>{{$t('page.company')}}</h4>
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
                                        <th scope="col">{{$t('page.name')}}</th>
                                        <th scope="col">{{$t('page.description')}}</th>
                                        <th scope="col">{{$t('page.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="data.length == 0"><td colspan="15" class="text-center">{{$t('page.no_data')}}</td></tr>
                                    <tr v-for="(item, index) in data" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{item.name}}</td>
                                        <td>{{item.description}}</td>
                                        <td class="py-1">
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
            name="company_modal"
            width="600"
            height="auto"
            :adaptive="true"
        >
            <div class="p-2" >
                <div class="card card-body">
                    <form action="" class="row g-3" method="post" @submit.prevent="submit()">
                        <h4 class="modal-title">{{$t(`page.${form_mode}`)}} {{$t('page.company')}}</h4>
                        <button type="button" class="btn-close" @click="$modal.hide('company_modal')"></button>
                        <div class="col-12">
                            <hr class="mt-0 mb-3" />
                            <label class="control-label">{{$t('page.name')}}</label>
                            <input class="form-control mt-1" type="text" v-model="form.name" required :placeholder="$t('page.name')" />
                            <has-error :form="form" field="name" />
                        </div>
                        <div class="col-12">
                            <label class="control-label">{{$t('page.description')}}</label>
                            <input class="form-control mt-1" type="text" v-model="form.description" :placeholder="$t('page.description')" />
                        </div>
                        <div class="text-right mt-3">
                            <hr class="mt-0 mb-3" />
                            <button type="button" class="btn btn-danger" @click="$modal.hide('company_modal')"><i class="bx bx-window-close"></i> {{$t('page.close')}}</button>
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
export default {
    middleware: 'auth',
    head () {
        return { title: this.$t('page.company') }
    },
    data() {
        return {
            data: [],
            form_mode: 'add',
            form: new Form({
                id: '',
                name: '',
                description: ''
            })
        }
    },
    mounted() {
        this.$store.dispatch('data/toggleSidebar', false);
        this.searchData();
    },
    methods: {
        searchData() {
            this.axios.get('/company/search').then(response => {
                this.data = response.data.data
            })
        },
        add() {
            this.form.reset();
            this.form_mode = 'add';
            this.$modal.show('company_modal');
        },
        edit(item) {
            this.form_mode = 'edit';
            this.form.id = item.id
            this.form.name = item.name
            this.form.description = item.description
            this.$modal.show('company_modal');
        },
        submit() {
            let uri = this.form_mode == 'edit' ? '/company/update' : '/company/create';
            this.form.post(uri).then(response => {
                this.searchData();
                this.form.reset()
                this.$modal.hide('company_modal')
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
                    this.axios.get(`/company/delete/${item.id}`).then(response => {
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
            })
        }
    }
}
</script>
