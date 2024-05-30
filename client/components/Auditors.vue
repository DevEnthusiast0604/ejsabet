<template>
    <div class="card">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="width:40px;">#</th>
                            <th scope="col">{{ $t('page.username') }}</th>
                            <th scope="col">{{ $t('page.company') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in auditors" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.username }}</td>
                            <td class="py-2">
                                <select class="form-control form-control-sm" @change="assignCompany($event, item)">
                                    <option value="">{{ $t('page.no_company') }}</option>
                                    <option v-for="company_item in companies" :key="company_item.id" :value="company_item.id" :selected="item.company_id === company_item.id">{{ company_item.name }}</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters } from "vuex"
export default {
    name: 'Auditors',
    data() {
        return {
            auditors: [],
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
        companies: 'data/companies'
    }),
    mounted() {
        this.$store.dispatch('data/getCompanies');
        this.init();
    },
    methods: {
        async init() {
            const response = await this.axios.post('/user/search', {role: 'auditor'});
            this.auditors = response.data.data;
        },
        async assignCompany(event, item) {
            const company_id = event.target.value;
            const response = await this.axios.post('/user/assign_company', {user_id: item.id, company_id: company_id});
            if (response.data.status === 'Success') {
                item.company_id = company_id;
                this.$toast.success(this.$t('message.successfully_done'));
            }
        }
    }
}
</script>