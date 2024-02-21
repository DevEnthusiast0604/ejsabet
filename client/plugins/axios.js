import Vue from "vue";
import axios from "axios";
import VueAxios from "vue-axios";
Vue.use(VueAxios, axios);
import Swal from "sweetalert2";

// process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

export default ({ app, store, redirect }) => {
    axios.defaults.baseURL = process.env.apiUrl;

    if (process.server) {
        return;
    }

    // Request interceptor
    axios.interceptors.request.use((request) => {
        request.baseURL = process.env.apiUrl;

        const token = store.getters["auth/token"];

        if (token) {
            request.headers.common.Authorization = `Bearer ${token}`;
        }

        const locale = store.getters["lang/locale"];
        if (locale) {
            request.headers.common["Accept-Language"] = locale;
        }

        request.headers.common["X-Requested-With"] = `XMLHttpRequest`;

        return request;
    });

    // Response interceptor
    axios.interceptors.response.use(
        (response) => response,
        async (error) => {
            const { status } = error.response || {};

            if (status >= 500) {
                Swal.fire({
                    icon: "error",
                    title: app.i18n.t("message.error_alert_title"),
                    text: app.i18n.t("message.error_alert_text"),
                    reverseButtons: true,
                    confirmButtonText: app.i18n.t("page.ok"),
                    cancelButtonText: app.i18n.t("page.cancel"),
                });
            }

            if (status === 401 && store.getters["auth/check"]) {
                Swal.fire({
                    icon: "warning",
                    title: app.i18n.t("message.token_expired_alert_title"),
                    text: app.i18n.t("message.token_expired_alert_text"),
                    reverseButtons: true,
                    confirmButtonText: app.i18n.t("page.ok"),
                    cancelButtonText: app.i18n.t("page.cancel"),
                }).then(() => {
                    store.dispatch("auth/logout");
                    if (window.location.pathname != "/") {
                        redirect({ name: "index" });
                    }
                });
            }

            if (status === 403) {
                await store.dispatch("auth/logout");
                console.log('403 Error');
                if (window.location.pathname != "/") {
                    redirect({ name: "index" });
                }
            }

            return Promise.reject(error);
        }
    );
};
