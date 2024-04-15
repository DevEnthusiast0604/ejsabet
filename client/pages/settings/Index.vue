<template>
    <div class="page-content">
        <div class="d-flex align-items-center mb-3">
            <h4 class="page-title"><i class="bx bx-cog"></i>{{$t('page.settings')}}</h4>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card card-body mb-2 mb-md-0">
                    <ul class="nav flex-column nav-pills">
                        <li v-for="tab in tabs" :key="tab.route" class="nav-item">
                            <router-link :to="{ name: tab.route }" class="nav-link" active-class="active">
                                <i class="bx" :class="tab.icon"></i>
                                {{ tab.name }}
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-9">
                <transition name="fade" mode="out-in">
                    <router-view />
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        middleware: "auth",

        computed: {
            tabs() {
                return [
                    {
                        icon: "bx-user",
                        name: this.$t("page.profile"),
                        route: "settings.profile",
                    },
                    {
                        icon: "bx-lock",
                        name: this.$t("page.password"),
                        route: "settings.password",
                    },
                    {
                        icon: "bx-mobile",
                        name: this.$t("page.google_authenticator"),
                        route: "settings.google_2fa",
                    },
                ];
            },
        },
    };
</script>

<style>
    .settings-card .card-body {
        padding: 0;
    }
</style>
