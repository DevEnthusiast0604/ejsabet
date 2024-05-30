<template>
    <div class="dropdown ms-1 topbar-head-dropdown header-item">
        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <!-- <img id="header-lang-img" src="assets/images/flags/us.svg" alt="Header Language" height="20"
                class="rounded"> -->
            {{ locales[locale] }}
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <a
                v-for="(value, key) in locales"
                :key="key" href="javascript:void(0);"
                class="dropdown-item notify-item language py-2"
                :title="value"
                @click.prevent="setLocale(key)"
            >
                <!-- <img :src="`~assets/images/flags/${key}.svg`" alt="user-image" class="me-2 rounded" height="18"> -->
                <span class="align-middle">{{ value }}</span>
            </a>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { loadMessages } from '~/plugins/i18n'

export default {
  computed: mapGetters({
    locale: 'lang/locale',
    locales: 'lang/locales'
  }),

  methods: {
    setLocale (locale) {
      if (this.$i18n.locale !== locale) {
        loadMessages(locale)

        this.$store.dispatch('lang/setLocale', { locale })
      }
    }
  }
}
</script>
