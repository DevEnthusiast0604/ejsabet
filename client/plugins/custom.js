import Vue from 'vue'

import VModal from 'vue-js-modal/dist/ssr.nocss'
import 'vue-js-modal/dist/styles.css'
Vue.use(VModal, { dynamicDefault: { draggable: true, resizable: true } })

// import VueLodash from 'vue-lodash'
// import lodash from 'lodash'
// Vue.use(VueLodash, { name: '$_', lodash })

import Paginate from 'vuejs-paginate'
Vue.component('paginate', Paginate)

import Multiselect from 'vue-multiselect'
Vue.component('multiselect', Multiselect)

import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
Vue.use(VueToast, {
    position: 'top-right',
    duration: 5000,
});

import loading from 'vuejs-loading-screen'
Vue.use(loading, {
    bg: '#41b883ad',
    slot: `
      <div class="px-5 py-3 bg-success rounded">
        <h3 class="text-3xl text-white"><i class="fas fa-spinner fa-spin"></i> Loading...</h3>
      </div>
    `
})

