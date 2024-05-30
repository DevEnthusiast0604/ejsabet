<template>
    <header id="page-topbar">
        <div class="layout-width" v-if="auth_user">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <router-link class="logo" :to="{name: 'index'}">
                            <img alt="Ejsabet" height="50" src="~/assets/images/logo.png" />
                        </router-link>
                    </div>

                    <button
                        type="button"
                        class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                        id="topnav-hamburger-icon"
                        @click="toggleSidebar"
                    >
                        <span class="hamburger-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </button>
                </div>
                <div class="ms-auto header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode" @click="toggleLayoutMode">
                        <i class="bx bx-moon fs-22"></i>
                    </button>
                </div>
                <div v-if="auth_user" class="d-flex align-items-center ms-3">
                    <h5 v-if="auth_user && auth_user.company" class="mb-0 me-5 text-white">{{auth_user.company.name}}</h5>
                    <locale-dropdown />
                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center">
                                <img class="rounded-circle header-profile-user" :src="auth_user.photo_url"
                                    alt="Header Avatar">
                                <span class="text-start ms-xl-2">
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{auth_user.username}}</span>
                                    <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">{{$t(`page.${auth_user.role}`)}}</span>
                                </span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <router-link :to="{ name: 'settings.profile' }" class="dropdown-item">
                                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">{{ $t('page.profile') }}</span>
                            </router-link>
                            <router-link v-if="$isSuperAdmin()" :to="{ name: 'site_status' }" class="dropdown-item">
                                <i class="bx bx-cog text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">{{ $t('page.site_status') }}</span>
                            </router-link>
                            <a href="javascript:;" class="dropdown-item" @click.prevent="logout">
                                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle" data-key="t-logout">{{$t('page.logout')}}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
    import { mapGetters } from "vuex";
    import LocaleDropdown from "./LocaleDropdown";

    export default {
        components: {
            LocaleDropdown,
        },

        data: () => ({
            appName: process.env.appName,
        }),

        computed: mapGetters({
            auth_user: "auth/user",
            layout_mode: 'data/layout_mode'
        }),

        methods: {
            async logout() {
                // Log out the user.
                await this.$store.dispatch("auth/logout");

                // Redirect to login.
                this.$router.push({ name: "index" });
            },
            toggleSidebar() {
                this.$store.dispatch('data/toggleSidebar');
            },
            toggleLayoutMode() {
                this.$store.dispatch('data/setLayoutMode');
            }
        },
    };
</script>
