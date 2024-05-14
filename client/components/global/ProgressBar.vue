<template>
    <div class="top-progress" :style="{width: progress + '%'}">
        <div v-if="progress > 1" class="progress-bar progress-bar-striped progress-bar-animated"></div>
    </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
    name: 'ProgressBar',
    computed: {
        ...mapGetters({
            progress: 'data/progress',
        }),
    }
}
</script>
<style lang="scss" scoped>
    .top-progress {
        position: fixed;
        top: 0px;
        left: 0px;
        right: 0px;
        width: 73.4294%;
        height: 3px;
        background-color: rgb(34, 153, 221);
        transition: all 350ms linear 0s;
        opacity: 1;
        z-index: 9999;
        .peg {
            display: block;
            position: absolute;
            right: 0px;
            width: 100px;
            height: 100%;
            opacity: 1;
            box-shadow: rgb(34, 153, 221) 0px 0px 10px, rgb(34, 153, 221) 0px 0px 5px;
            transform: rotate(3deg) translate(0px, -4px);
        }
    }
</style>