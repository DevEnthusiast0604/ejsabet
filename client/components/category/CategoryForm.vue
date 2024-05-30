<template>
    <form class="row g-3" action="" method="post" @submit.prevent="submit()">
        <h4 class="modal-title">{{$t(`page.${mode}`)}} {{$t('page.category')}}</h4>
        <button type="button" class="btn-close" @click="$modal.hide('category_modal')"></button>
        <div class="col-12">
            <hr class="mt-0 mb-3" />
            <label class="control-label">{{$t('page.name')}}</label>
            <input class="form-control mt-1" type="text" v-model="form.name" required :placeholder="$t('page.name')" />
            <has-error :form="form" field="name" />
        </div>
        <div class="col-12">
            <label class="control-label">{{$t('page.type')}}</label>
            <select class="form-select mt-1" v-model="form.type">
                <option value="" hidden>{{$t('page.type')}}</option>
                <option value="expense">{{$t('page.expense')}}</option>
                <option value="incoming">{{$t('page.incoming')}}</option>
                <option value="transfer">{{$t('page.transfer')}}</option>
            </select>
            <has-error :form="form" field="type" />
        </div>
        <div class="col-12" v-if="form.type == 'expense' || form.type == 'transfer'">
            <label class="control-label">{{$t('page.withdraw_from')}}</label>
            <select class="form-select mt-1" v-model="form.account">
                <option value="" hidden>{{$t('page.select_account')}}</option>
                <option :value="item.id" v-for="(item, index) in accounts" :key="index">{{item.name}}</option>
            </select>
            <has-error :form="form" field="account" />
        </div>
        <div class="col-12" v-if="form.type == 'incoming' || form.type == 'transfer'">
            <label class="control-label">{{$t('page.target_account')}}</label>
            <select class="form-select mt-1" v-model="form.target">
                <option value="" hidden>{{$t('page.select_account')}}</option>
                <option :value="item.id" v-for="(item, index) in accounts" :key="index">{{item.name}}</option>
            </select>
            <has-error :form="form" field="target" />
        </div>
        <div class="col-12">
            <label class="control-label">{{$t('page.comment')}}</label>
            <input class="form-control mt-1" type="text" v-model="form.comment" :placeholder="$t('page.comment')" />
        </div>
        <div class="col-12 text-right">
            <hr class="mb-3 mt-0" />
            <button type="button" class="btn btn-danger" @click="$modal.hide('category_modal')"><i class="bx bx-window-close"></i> {{$t('page.close')}}</button>
            <button type="submit" class="btn btn-primary ml-1" :class="{'btn-loading': form.busy}" :disabled="form.busy"><i class="bx bx-save"></i> {{$t('page.save')}}</button>
        </div>
    </form>
</template>
<script>
import Form from "vform"
import {mapGetters} from "vuex"
export default {
    props: ['mode', 'category'],
    data() {
        return {
            accounts: [],
            form: new Form({
                id: '',
                name: '',
                type: '',
                account: '',
                target: '',
                comment: ''
            }),        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
    }),
    mounted() {
        this.getAccounts();
        if (this.mode == 'edit') {
            this.getDetail();
        }
    },
    methods: {
        getAccounts() {
            this.axios.get('/account/search').then(response => {
                let accounts = response.data.data;
                if (this.$isAdmin()) {
                    this.accounts = accounts.filter(i => i.company_id == this.auth_user.company_id);
                } else {
                    this.accounts = accounts
                }
                // if(this.accounts[0] && this.mode == 'add') {
                //     this.form.account = this.accounts[0].id;
                //     this.form.target = this.accounts[0].id;
                // }
            })
        },
        getDetail() {
            this.form.id = this.category.id
            this.form.name = this.category.name
            this.form.account = this.category.from_account_id
            this.form.target = this.category.to_account_id
            this.form.type = this.category.type
            this.form.comment = this.category.comment
        },
        submit() {
            let uri = this.mode == 'edit' ? '/category/update' : '/category/create';
            this.form.post(uri).then(response => {
                if (response.data.status == 'Success') {
                    let message = this.mode == 'edit' ? this.$t('message.updated_successfully') : this.$t('message.created_successfully');
                    this.$toast.success(message);
                    this.form.reset();
                    this.$modal.hide('category_modal')
                    let parent_component = this.$parent.$parent;
                    console.log(parent_component.$options.name)
                    if (parent_component.$options.name == 'Category') {
                        parent_component.searchData();
                    }
                }
            })
        },
    }
}
</script>