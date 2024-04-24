<template>
    <div class="card">
        <div class="card-header">{{$t('page.your_info')}}</div>
        <div class="card-body">
            <form @submit.prevent="update" @keydown="form.onKeydown($event)">
                <!-- Name -->
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label text-md-right">{{ $t('page.username') }}</label>
                    <div class="col-md-7">
                        <input v-model="form.username" :class="{ 'is-invalid': form.errors.has('username') }" type="text" name="username" class="form-control" :placeholder="$t('page.username')" />
                        <has-error :form="form" field="username" />
                    </div>
                </div>

                <!-- Email -->
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label text-md-right">{{ $t('page.email') }}</label>
                    <div class="col-md-7">
                        <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email" name="email" class="form-control" :placeholder="$t('page.email')" />
                        <has-error :form="form" field="email" />
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label text-md-right">{{ $t('page.phone_number') }}</label>
                    <div class="col-md-7">
                        <input v-model="form.phone_number" :class="{ 'is-invalid': form.errors.has('phone_number') }" type="text" name="phone_number" class="form-control" :placeholder="$t('page.phone_number')" />
                        <has-error :form="form" field="phone_number" />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row mb-3">
                    <div class="col-md-9 ms-md-auto">
                        <v-button :loading="form.busy" type="primary">
                            <i class="bx bx-save"></i>
                            {{ $t('page.update') }}
                        </v-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import Form from "vform";
    import { mapGetters } from "vuex";

    export default {
        scrollToTop: false,

        data: () => ({
            form: new Form({
                username: "",
                email: "",
                phone_number: "",
            }),
        }),

        head() {
            return { title: this.$t("page.settings") };
        },

        computed: mapGetters({
            user: "auth/user",
        }),

        created() {
            // Fill the form with user data.
            this.form.keys().forEach((key) => {
                this.form[key] = this.user[key];
            });
        },

        methods: {
            update() {
                this.form.patch("/settings/profile").then(({ data: user }) => {
                    this.$store.dispatch("auth/updateUser", { user });
                    this.$toast.success(this.$t('message.updated_successfully'))
                });
            },
        },
    };
</script>
