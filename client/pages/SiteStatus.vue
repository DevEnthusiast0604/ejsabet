<template>
    <div class="page-content">
        <div class="d-flex align-items-center mb-3">
            <h4 class="page-title"><i class="bx bx-sitemap"></i>{{$t('page.site_status')}}</h4>
        </div>
        <div class="col-12">
            <div class="card card-body mb-3">
                <button type="button" class="btn btn-lg btn-warning mb-2 font-weight-bold" @click="disableSiteStatus()">{{$t('page.disable_site')}}</button>
            </div>
            <div class="card card-body">
                <h4 class="mb-3">{{$t('page.disable_time_for_audit')}}</h4>
                <div class="d-md-flex align-items-center">
                    <div class="mb-2">
                        <label class="mb-0 me-2">{{$t('page.start')}}:</label><vue-timepicker v-model="form.start"></vue-timepicker>
                    </div>
                    <div class="mb-2">
                        <label class="mb-0 me-2 ms-md-2">{{$t('page.end')}}:</label><vue-timepicker v-model="form.end"></vue-timepicker>
                    </div>
                    <div class="mb-2 ms-md-2">
                        <button type="button" class="btn btn-sm btn-primary" @click="addTime"><fa icon="plus" />&nbsp;{{$t('page.add')}}</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-body border p-3 mb-3" v-for="(item, index) in timeline" :key="index" >
                            <h6 class="mb-0 d-flex align-items-center">
                                {{$t('page.from')}}&nbsp;&nbsp;{{item.start}}&nbsp;&nbsp;&nbsp;&nbsp;{{$t('page.to')}}&nbsp;&nbsp;{{item.end}}
                                <fa icon="times" class="btn-remove" @click="timeline.splice(index, 1)" />
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-body">
                <div class="row mt-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <h4 class="mb-3">{{$t('page.allowed_ip_address')}}</h4>
                        <div class="mb-3">
                            <form class="d-flex" @submit.prevent="saveIpAddress()">
                                <input type="text" class="form-control" v-model="ipForm.ip_address" :placeholder="$t('page.ip_address')" required maxlength="15" />
                                <select class="form-control ms-2" v-model="ipForm.company_id">
                                    <option value="">{{ $t('page.company') }}</option>
                                    <option v-for="(company, index) in companies" :key="index" :value="company.id">{{ company.name }}</option>
                                </select>
                                <button type="submit" class="btn btn-primary ms-2">{{ $t('page.save') }}</button>
                                <button v-if="ipForm.id" type="button" class="btn btn-danger ms-2" @click="ipForm.reset()">{{ $t('page.cancel') }}</button>
                            </form>
                            <has-error :form="ipForm" field="ip_address" />
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ $t('page.ip_address') }}</th>
                                        <th scope="col">{{ $t('page.company') }}</th>
                                        <th scope="col" style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in allowed_ips" :key="index">
                                        <td>{{ item.ip_address }}</td>
                                        <td>{{ item.company ? item.company.name : '' }}</td>
                                        <td class="py-2">
                                            <a href="javascript:;" class="btn-edit" @click.prevent="editIpAddress(item)"><i class="bx bx-edit"></i></a>
                                            <a href="javascript:;" class="btn-delete" @click.prevent="removeIpAddress(item, index)"><i class="bx bx-trash"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-body">
                <h4 class="mb-3">{{ $t('page.upload_database_backup') }}</h4>
                <div class="row">
                    <div class="col-md-6 col-lg-5 col-xl-4">
                        <form @submit.prevent="uploadBackup()">
                            <div class="d-flex">
                                <input type="file" accept=".sql" class="form-control" required @change="changeBackupFile" />
                                <button type="submit" class="btn btn-primary ms-2 d-inline-flex align-items-center" :class="{'btn-loading': backupForm.busy}">
                                    <i class="ri-upload-cloud-line"></i>&nbsp;
                                    {{ $t('page.upload') }}
                                </button>
                            </div>
                            <div v-if="backupForm.progress" class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" :style="{width: `${backupForm.progress.percentage}%`}"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {mapGetters} from "vuex"
import Swal from 'sweetalert2'
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'
import Form from 'vform';
export default {
    middleware: 'auth',
    components: {
        VueTimepicker
    },
    data() {
        return {
            timeline: [],
            form: {
                start: '',
                end: '',
            },
            ip_address: '',
            backupForm: new Form({
                backup_file: null,
            }),
            ipForm: new Form({
                id: '',
                ip_address: '',
                company_id: '',
            }),
        }
    },
    computed: mapGetters({
        auth_user: 'auth/user',
        allowed_ips: 'auth/allowed_ips',
        companies: 'data/companies',
    }),
    watch: {
        timeline(value) {
            this.save()
        },
    },
    mounted() {
        this.getDisableTime();
        this.$store.dispatch('data/getCompanies');
    },
    methods: {
        async disableSiteStatus() {
            const result = await Swal.fire({
                icon: 'warning',
                title: this.$t('message.are_you_sure'),
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: this.$t('page.ok'),
                cancelButtonText: this.$t('page.cancel')
            })
            if (result.isConfirmed) {
                const response = await this.axios.post(`/site_status/disable`)
                if (response.data.status == 'Success') {
                    this.$toast.success(this.$t('message.successfully_done'));
                    await this.$store.dispatch('auth/logout')
                    location.href = '/login';
                } else {
                    this.$toast.error(this.$t('message.something_went_wrong'));
                }
            }
        },
        addTime() {
            if (!this.form.start || !this.form.end) {
                this.$toast.error('Input time correctly');
                return false
            }
            this.timeline.push({start: this.form.start, end: this.form.end})
            this.form = {
                start: '',
                end: '',
            }
        },
        async getDisableTime() {
            const response = await this.axios.post('/setting/get', {key: 'site_disable_time'})
            if (response.data.data && Array.isArray(JSON.parse(response.data.data))) {
                this.timeline = JSON.parse(response.data.data)
            }
        },
        async save() {
            const response = await this.axios.post('/setting/set', {key: 'site_disable_time', value: JSON.stringify(this.timeline)})
        },
        editIpAddress(item) {
            this.ipForm.id = item.id;
            this.ipForm.ip_address = item.ip_address;
            this.ipForm.company_id = item.company_id;
        },
        async saveIpAddress() {
            try {
                const response = await this.ipForm.post('/company_ip/save');
                if (response.data.status === 'Success') {
                    this.ipForm.reset();
                    this.$store.dispatch('auth/fetchAllowedIps');
                }
            } catch (error) {

            }
        },
        async removeIpAddress(item, index) {
            const response = await this.axios.delete(`/company_ip/delete/${item.id}`);
            if (response.data.status === 'Success') {
                this.$store.dispatch('auth/fetchAllowedIps');
            }
        },
        changeBackupFile(e) {
            this.backupForm.backup_file = e.target.files[0];
        },
        async uploadBackup() {
            try {
                const response = await this.backupForm.post('/backup/upload');
                if (response.data.status === 'Success') {
                    this.$toast.success(this.$t('message.successfully_done'));
                    this.backupForm.reset();
                }
            } catch (error) {
                this.$toast.error(this.$t('message.error_alert_text'));
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    .btn-remove {
        cursor: pointer;
        margin-left: auto;
    }
</style>