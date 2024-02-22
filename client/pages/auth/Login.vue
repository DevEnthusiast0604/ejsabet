<template>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card overflow-hidden">
                    <div class="row g-0">
                        <div class="col-lg-6">
                            <div class="p-lg-5 p-4 auth-one-bg h-100">
                                <div class="bg-overlay"></div>
                                <div class="position-relative h-100 d-flex flex-column">
                                    <div class="mb-4">
                                        <a href="javascript:;" class="d-block text-white fs-24 fw-bold">
                                            {{appName}}
                                        </a>
                                    </div>
                                    <div class="mt-auto">
                                        <div class="mb-3">
                                            <i class="ri-double-quotes-l display-4 text-success"></i>
                                        </div>

                                        <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-indicators">
                                                <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                            </div>
                                            <div class="carousel-inner text-center text-white-50 pb-5">
                                                <div class="carousel-item active">
                                                    <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                </div>
                                                <div class="carousel-item">
                                                    <p class="fs-15 fst-italic">" The theme is really great with an amazing customer support."</p>
                                                </div>
                                                <div class="carousel-item">
                                                    <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end carousel -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->

                        <div class="col-lg-6">
                            <div v-if="step === 'login'" class="p-4 p-lg-5">
                                <div>
                                    <h5 class="text-primary">{{$t('page.sign_in')}}</h5>
                                    <p class="text-muted">{{$t('message.enter_your_credentials_below')}}</p>
                                </div>

                                <div class="mt-4">
                                    <form  @submit.prevent="login" @keydown="form.onKeydown($event)">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">{{$t('page.username')}}</label>
                                            <input v-model="form.username" :class="{ 'is-invalid': form.errors.has('username') }" type="text" name="username" class="form-control" :placeholder="$t('page.username')" />
                                            <has-error :form="form" field="username" />
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password-input">{{$t('page.password')}}</label>
                                            <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" type="password" name="password" class="form-control" :placeholder="$t('page.password')" />
                                            <has-error :form="form" field="password" />
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="auth-remember-check" v-model="remember" />
                                            <label class="form-check-label" for="auth-remember-check">{{$t('page.remember_me')}}</label>
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-success w-100" :class="{'btn-loading': form.busy}" :disabled="form.busy">{{$t('page.sign_in')}}</button>
                                        </div>

                                        <!-- <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 title">Sign In with</h5>
                                            </div>

                                            <div>
                                                <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
                                                <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
                                                <button type="button" class="btn btn-dark btn-icon waves-effect waves-light"><i class="ri-github-fill fs-16"></i></button>
                                                <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button>
                                            </div>
                                        </div> -->
                                    </form>
                                </div>
                            </div>
                            <div class="p-lg-5 p-4" v-if="step == '2fa'">
                                <div class="text-center">
                                    <h3 class="">{{$t('page.google_authenticator')}}</h3>
                                    <p>{{$t('message.input_one_time_password')}}</p>
                                </div>
                                <div class="login-separater text-center mb-4">
                                    <hr />
                                </div>
                                <div class="form-body">
                                    <form class="row g-3" @submit.prevent="checkOTP" @keydown="otp_form.onKeydown($event)">
                                        <div class="col-12 mb-3">
                                            <input type="text" v-model="otp_form.one_time_password" class="form-control" :class="{ 'is-invalid': form.errors.has('one_time_password') }" :placeholder="$t('page.one_time_password')" />
                                            <has-error :form="otp_form" field="one_time_password" />
                                        </div>
                                        <div class="mt-3 d-grid">
                                            <button type="submit" :class="{'btn-loading': otp_form.busy}" class="btn btn-block btn-primary">{{$t('page.verify')}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    </div>
</template>

<script>
    import Form from "vform";

    export default {
        layout: 'auth',
        middleware: "guest",

        data: () => ({
            appName: process.env.appName,
            form: new Form({
                email: "",
                password: "",
            }),
            remember: false,
            step: 'login',
            token: '',
            auth_user: null,
            otp_form: new Form({
                google2fa_secret: '',
                one_time_password: "",
            }),
        }),

        head() {
            return { title: this.$t("page.sign_in") };
        },

        mounted() {
            document.body.classList.add('bg-login')
        },

        beforeDestroy() {
            document.body.classList.remove('bg-login')
        },

        methods: {
            async login() {
                let data;

                // Submit the form.
                try {
                    const response = await this.form.post("/login");
                    data = response.data;
                } catch (e) {
                    return;
                }

                this.auth_user = data.auth_user;

                if (this.auth_user.enable_google2fa) {
                    this.otp_form.google2fa_secret = this.auth_user.google2fa_secret
                    this.step = '2fa';
                    this.token = data.token;
                } else {
                    this.saveToken(data.token)
                }
            },
            async checkOTP() {
                let data;

                // Submit the form.
                try {
                    const response = await this.otp_form.post("/2fa");
                    data = response.data;
                } catch (e) {
                    return;
                }

                this.saveToken(this.token)
            },
            async saveToken(token) {

                // Save the token.
                this.$store.dispatch("auth/saveToken", {
                    token: token,
                    remember: this.remember,
                });

                // Fetch the user.
                await this.$store.dispatch("auth/fetchUser");

                // Redirect home.
                if (this.$isAuditor()) {
                    this.$router.push({name: 'audit'});
                } else if (this.$isSubAdmin()) {
                    this.$router.push({ name: "daily_transaction"});
                } else {
                    this.$router.push({ name: "transaction" });
                }
            },
        },
    };
</script>
<style lang="scss" scoped>
    .auth-logo {
        color: #FFF;
        font-size: 40px;
        font-weight: bold;
    }
</style>
