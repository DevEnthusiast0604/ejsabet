<template>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mt-sm-5 mb-4 text-white-50">
                    <div>
                        <a href="javascript:;" class="d-inline-block auth-logo">
                            {{appName}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card mt-4">
                    <div class="card-body p-4">
                        <div class="text-center mt-2">
                            <h5 class="text-primary">{{$t('page.activate')}}</h5>
                            <p class="text-muted">{{$t('page.enter_your_username_password')}}</p>
                        </div>
                        <div class="p-2 mt-4">
                            <form class="pt-3" @submit.prevent="activate" @keydown="form.onKeydown($event)" autocomplete="off">
                                <input type="text" name="username" autocomplete="off" class="d-none" />
                                <div class="mb-3">
                                    <label for="username" class="form-label">{{$t('page.username')}}</label>
                                    <input type="text" v-model="form.username" class="form-control" :class="{ 'is-invalid': form.errors.has('username') }" :placeholder="$t('page.username')" />
                                    <has-error :form="form" field="username" />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="password-input">{{$t('page.password')}}</label>
                                    <input type="text" v-model="form.password" class="form-control password" name="password" :class="{ 'is-invalid': form.errors.has('password') }" :placeholder="$t('page.password')" />
                                    <has-error :form="form" field="password" />
                                </div>
                                <div class="mt-3">
                                    <button type="submit" :class="{'btn-loading': form.busy}" class="btn w-100 btn-primary font-weight-medium">{{$t('page.activate')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Form from "vform";
    import Swal from 'sweetalert2'
    export default {
        name: 'Activate',
        layout: "auth",
        middleware: "guest",
        data: () => ({
            appName: process.env.appName,
            form: new Form({
                username: "",
                password: "",
            }),
        }),
        methods: {
            async activate() {
                const response = await this.form.post("/site_status/enable");
                if (response.data.status == 'Success') {
                    Swal.fire({
                        icon: 'warning',
                        title: this.$t('message.site_activated'),
                        text: this.$t('message.please_login_again'),
                        showCancelButton: false,
                        confirmButtonText: this.$t('page.ok'),
                    }).then(result => {
                        this.$router.push({name: 'login'})
                    })
                }
            },
        },
    };
</script>
<style lang="scss" scoped>
    .password {
        -webkit-text-security: disc;
    }
    .auth-logo {
        color: #FFF;
        font-size: 40px;
        font-weight: bold;
    }
</style>
