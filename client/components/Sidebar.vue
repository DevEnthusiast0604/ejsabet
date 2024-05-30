<template>
    <div class="app-menu navbar-menu">
        <!-- LOGO -->
        <div class="navbar-brand-box">
            <!-- Dark Logo-->
            <a href="/" class="logo logo-dark">
                <span class="logo-sm">
                    {{ appName }}
                </span>
                <span class="logo-lg">
                    {{ appName }}
                </span>
            </a>
            <!-- Light Logo-->
            <a href="/" class="logo logo-light">
                <span class="logo-sm">
                    {{ appName }}
                </span>
                <span class="logo-lg">
                    {{ appName }}
                </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>

        <div v-if="auth_user" id="scrollbar">
            <div class="container-fluid">
                <div id="two-column-menu"></div>
                <ul class="navbar-nav" id="navbar-nav">
                    <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                    <li class="nav-item">
                        <a href="javascript:;"
                             class="nav-link menu-link collapsed"
                             :class="{active: ['transaction', 'daily_transaction', 'transaction_approved'].includes(route)}"
                             data-bs-toggle="collapse"
                             @click="toggleCollapse('transaction')"
                        >
                            <i class="ri-file-list-line"></i>{{$t('page.transaction')}}
                        </a>
                        <div class="collapse menu-dropdown" ref="collapse_transaction">
                            <ul class="nav nav-sm flex-column">
                                <li v-if="visibleTransaction" class="nav-item">
                                    <router-link :to="{name: 'transaction'}" class="nav-link" :class="{'active': route == 'transaction'}">
                                        <i class="ri-file-list-line"></i>
                                        <span data-key="t-transaction">{{$t('page.transaction')}}</span>
                                    </router-link>
                                </li>
                                <li v-if="visibleTransaction || $isSubAdmin()" class="nav-item">
                                    <router-link :to="{name: 'daily_transaction'}" class="nav-link" :class="{'active': route == 'daily_transaction'}">
                                        <i class="ri-calendar-event-line"></i>
                                        <span data-key="t-daily_transaction">{{$t('page.daily_transaction')}}</span>
                                    </router-link>
                                </li>
                                <li v-if="$isSuperAdmin()" class="nav-item">
                                    <router-link :to="{name: 'transaction_approved'}" class="nav-link" :class="{'active': route == 'transaction_approved'}">
                                        <i class="ri-file-list-line"></i>
                                        <span data-key="t-transaction">{{$t('page.approved_transactions')}}</span>
                                    </router-link>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li v-if="$isSuperAdmin() || $isAdmin()" class="nav-item">
                        <router-link :to="{name: 'category'}" class="nav-link menu-link" :class="{'active': route == 'category'}">
                            <i class="ri-git-merge-line"></i>
                            <span data-key="t-category">{{$t('page.category')}}</span>
                        </router-link>
                    </li>
                    <template v-if="$isSuperAdmin()">
                        <li class="nav-item">
                            <router-link :to="{name: 'company'}" class="nav-link menu-link" :class="{'active': route == 'company'}">
                                <i class="ri-layout-grid-line"></i>
                                <span data-key="t-company">{{$t('page.company')}}</span>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{name: 'account'}" class="nav-link menu-link" :class="{'active': route == 'account'}">
                                <i class="ri-bank-card-2-line"></i>
                                <span data-key="t-account">{{$t('page.account')}}</span>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{name: 'user'}" class="nav-link menu-link" :class="{'active': route == 'user'}">
                                <i class="ri-user-line"></i>
                                <span data-key="t-user">{{$t('page.user')}}</span>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{name: 'advanced_delete'}" class="nav-link menu-link" :class="{'active': route == 'advanced_delete'}">
                                <i class="ri-delete-bin-line"></i>
                                <span data-key="t-advanced_delete">{{$t('page.advanced_delete')}}</span>
                            </router-link>
                        </li>
                    </template>
                    <li v-if="$isSuperAdmin() || $isAuditor()" class="nav-item">
                        <router-link :to="{name: 'audit'}" class="nav-link menu-link" :class="{'active': route == 'audit'}">
                            <i class="ri-calendar-check-line"></i>
                            <span data-key="t-audit">{{$t('page.auditor')}}</span>
                        </router-link>
                    </li>
                    <li v-if="$isSuperAdmin()" class="nav-item">
                        <router-link :to="{name: 'dashboard'}" class="nav-link menu-link" :class="{'active': route == 'dashboard'}">
                            <i class="ri-dashboard-2-line"></i>
                            <span data-key="t-dashboards">{{$t('page.dashboard')}}</span>
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from "vuex"
export default {
    data() {
        return {
            appName: process.env.appName,
            route: this.$route.name,
        }
    },
	computed: {
        visibleTransaction() {
            if ((this.$isAuditor() && !this.auth_user.company_id) || this.$isSubAdmin()) return false;
            return true;
        },
        ...mapGetters({
            auth_user: 'auth/user',
            sidebar_collapsed: 'data/sidebar_collapsed'
        })
    },
    watch: {
        '$route': function (newRoute) {
            this.route = newRoute.name
        }
    },
    methods: {
        toggleSidebar() {
            this.$store.dispatch('data/toggleSidebar');
        },
        toggleCollapse(menu) {
            this.$refs[`collapse_${menu}`].classList.toggle('show');
        }
    }
}
</script>
