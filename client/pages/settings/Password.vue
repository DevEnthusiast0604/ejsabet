<template>
    <div class="card">
        <div class="card-header">{{$t('page.your_password')}}</div>
        <div class="card-body">
            <form @submit.prevent="update" @keydown="form.onKeydown($event)">
                <alert-success :form="form" :message="$t('page.password_updated')" />

                <!-- Password -->
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label control-label text-md-right">{{ $t('page.new_password') }}</label>
                    <div class="col-md-7">
                        <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" type="password" name="password" class="form-control" :placeholder="$t('page.new_password')" />
                        <span class="text-info">{{$t('message.password_rule')}}</span>
                        <has-error :form="form" field="password" />
                    </div>
                </div>

                <!-- Password Confirmation -->
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label control-label text-md-right">{{ $t('page.confirm_password') }}</label>
                    <div class="col-md-7">
                        <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" type="password" name="password_confirmation" class="form-control" :placeholder="$t('page.confirm_password')" />
                        <has-error :form="form" field="password_confirmation" />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row mb-3">
                    <div class="col-md-9 ms-md-auto">
                        <v-button :loading="form.busy" type="primary"> <i class="bx bx-save"></i> {{ $t('page.update') }} </v-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import Form from "vform";

    export default {
        scrollToTop: false,

        data: () => ({
            form: new Form({
                password: "",
                password_confirmation: "",
            }),
        }),

        head() {
            return { title: this.$t("page.settings") };
        },

        methods: {
            update() {
                this.form.patch("/settings/password").then(() => {
                    this.form.reset();
                    this.$toast.success(this.$t('message.updated_successfully'))
                });
            },
        },
    };
</script>
