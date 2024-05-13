<template>
    <form action="" class="row g-3" @submit.prevent="submit">
        <h4 class="modal-title">{{ $t(`page.${mode}`) }} {{ $t('page.transaction') }}</h4>
        <button type="button" class="btn-close" @click="$modal.hide('transaction_modal')"></button>
        <div class="col-12">
            <hr class="mb-3 mt-0" />
            <label class="control-label mb-1">{{ $t('page.category') }}</label>
            <multiselect v-model="selected_category" :options="categories" :multiple="false" :preserve-search="true"
                :placeholder="$t('page.select_category')" label="name" track-by="id" :show-labels="false">
                <span slot="noOptions" class="no_option_slot d-none"></span>
                <span slot="noResult">{{ $t('message.no_elements_found') }}</span>
            </multiselect>
            <has-error :form="form" field="category" />
        </div>
        <div class="col-12">
            <label class="control-label">{{ $t('page.date') }}</label>
            <datepicker v-model="form.date" input-class="form-control mt-1" format="yyyy-MM-dd" :use-utc="true"
                :typeable="true" :placeholder="$t('page.date')" :disabled-dates="disabledDates"></datepicker>
            <has-error :form="form" field="date" />
        </div>
        <div class="col-12">
            <label class="control-label">{{ $t('page.amount') }}</label>
            <input type="text" class="form-control mt-1" v-model="form.amount" :placeholder="$t('page.amount')" />
            <has-error :form="form" field="amount" />
        </div>
        <div class="col-12">
            <label for="formFile" class="control-label">{{ $t('page.attachment') }}</label>
            <div class="d-flex">
                <input class="form-control mt-1" type="file" id="formFile" data-v-66434748="" multiple="multiple"
                    accept="image/*, application/pdf" @change="handleFile" />
                <!-- <input type="file" accept="image/*" ref="fileInput" capture="camera" class="d-none" />
                <i v-if="isMobile" class="bx bxs-camera fs-23 mt-3 ml-1" @click="openCamera"></i> -->
            </div>
            <has-error :form="imageForm" field="images" />
            <div v-if="mode == 'edit' && transaction.images.length" class="mt-1">
                <delete-image :images="transaction.images"></delete-image>
            </div>
        </div>
        <div class="col-12">
            <label class="control-label">{{ $t('page.description') }}</label>
            <input type="text" class="form-control mt-1" v-model="form.description"
                :placeholder="$t('page.description')" />
        </div>
        <div class="col-12">
            <hr class="mb-3 mt-0" />
            <button type="button" class="btn btn-danger" @click="close()">
                <i class="bx bx-window-close"></i>
                {{ $t('page.close') }}
            </button>
            <button type="submit" class="btn btn-primary ms-2" :class="{ 'btn-loading': form.busy }"
                :disabled="form.busy">
                <i class="bx bx-save"></i>
                {{ $t('page.save') }}
            </button>
        </div>
    </form>
</template>
<script>
import Form from "vform"
import { mapGetters } from "vuex"
import Datepicker from 'vuejs-datepicker';
import DeleteImage from "./DeleteImage.vue"
export default {
    props: ['mode', 'transaction', 'categoriesForm'],
    components: {
        Datepicker,
        DeleteImage
    },
    data() {
        return {
            categories: [],
            accounts: [],
            form: new Form({
                id: '',
                category: '',
                date: this.$moment().format('YYYY-MM-DD'),
                amount: '',
                description: '',
                has_images: false,
            }),
            imageForm: new Form({
                id: '',
                images: null,
            }),
            selected_category: null,
            latest_audit_date: null,
            imageSelected: false,
            maxImageLimit: 25,
        }
    },
    computed: {
        disabledDates() {
            if (this.latest_audit_date) {
                return {
                    to: new Date(this.latest_audit_date),
                }
            } else {
                return {
                    to: new Date(1970, 1, 1),
                }
            }
        },
        isMobile() {
            return screen.width <= 760;
        },
        ...mapGetters({
            auth_user: 'auth/user',
        })
    },
    watch: {
        selected_category: function (newCat) {
            this.form.category = newCat ? newCat.id : ''
        },
    },
    async mounted() {
        this.categories = this.categoriesForm;
        // await this.getCategories();
        if (this.mode == 'edit') {
            this.getDetail();
        }
        this.getLatestAuditDate();
    },
    methods: {
        openCamera() {
            this.$refs.fileInput.click()
        },
        async getCategories() {
            const user_id = this.$isAdmin() || this.$isSubAdmin() ? this.auth_user.id : '';
            const response = await this.axios.post('/category/search', { user_id: user_id, from: 'transaction_form' });
            this.categories = response.data.data
        },
        getDetail() {
            this.form.id = this.transaction.id
            this.form.type = this.transaction.type
            this.form.date = this.transaction.timestamp
            this.form.category = this.transaction.category_id
            this.form.amount = this.transaction.amount
            this.form.description = this.transaction.description

            this.selected_category = this.transaction.category
        },
        handleFile(event) {
            const files = event.target.files || event.dataTransfer.files;
            if (files.length > this.maxImageLimit) {
                this.$toast.error(this.$t('message.you_can_upload_maximum_images', { images: this.maxImageLimit }));
                event.target.value = null;
                return false;
            }
            if (!files.length) return false;
            this.imageForm.images = event.target.files;
        },
        async submit() {
            const uri = this.mode == 'edit' ? '/transaction/update' : '/transaction/create';

            try {
                let response;
                if (this.imageForm.images) this.form.has_images = true;

                if (this.mode == 'edit') {
                    this.transaction.id = this.form.id;
                    this.transaction.type = this.form.type
                    this.transaction.timestamp = this.form.date
                    this.transaction.category_id = this.form.category
                    this.transaction.amount = this.form.amount
                    this.transaction.description = this.form.description

                    this.transaction.category = this.selected_category
                    this.$emit('updateImage', {form:this.form, imageForm:this.imageForm})
                } else {
                    await this.form.post(uri).then(response => {
                        this.imageForm.id = response.data.data.id
                        this.$emit('submitted', response.data.data);
                    });
                }

                this.$store.dispatch('data/getBalance');
                if (this.imageForm.images && this.imageForm.images.length > 0 && this.mode !== 'edit') {
                    this.$emit('start-uploading', this.imageForm);
                } else {
                    this.$toast.success(this.$t(this.mode == 'edit' ? 'message.updated_successfully' : 'message.created_successfully'));

                    // Send notification to sub admin
                    const joinData = this.$fire.database.ref(`transactions/created`).push();
                    joinData.set({
                        transaction_id: response.data.data.id,
                        user_id: this.auth_user.id,
                    });
                }

                this.$modal.hide('transaction_modal');
            } catch (error) {
                //this.$toast.error(this.$t('message.error_alert_text'));
                this.$modal.hide('transaction_modal');
            }
        },
        close() {
            if (this.mode == 'add') {
                this.$router.back()
            } else {
                this.$modal.hide('transaction_modal')
            }
        },
        async getLatestAuditDate() {
            const { data } = await this.axios.get('/audit/get_latest_audit_date');
            this.latest_audit_date = data.data
        }
    }
}
</script>