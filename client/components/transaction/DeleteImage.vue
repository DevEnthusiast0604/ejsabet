<template>
    <div>
        <div class="d-flex align-items-center" v-viewer>
            <div class="image-container" v-for="(img, index) in images" :key="index">
                <img :src="img.src" class="item-image me-2" />
                <i class="ri-close-line btn-delete"  @click="remove(img, index)"></i>
            </div>
        </div>
    </div>
</template>
<script>
import 'viewerjs/dist/viewer.css'
import { directive as viewer } from "v-viewer"
import Swal from 'sweetalert2'
export default {
    props: ['images'],
    directives: {
        viewer: viewer({
            debug: true,
        }),
    },
    methods: {
        remove(item, index) {
            Swal.fire({
                icon: 'warning',
                title: this.$t('message.are_you_sure'),
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: this.$t('page.ok'),
                cancelButtonText: this.$t('page.cancel')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.images.splice(index, 1);
                    this.axios.delete(`/image/delete/${item.id}`);
                }
            })
        },
    }
}
</script>
<style lang="scss" scoped>
    .image-container {
        width: 40px;
        height: 40px;
        position: relative;
        display: flex;
        align-items: center;
        margin-right: 10px;
        img {
            width: 40px;
            height: 40px;
        }
        .btn-delete {
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 13px;
            color: #FFF !important;
            border: solid 1px #FF4747;
            border-radius: 20px;
            display: block;
            width: 14px;
            height: 14px;
            line-height: 14px;
            text-align: center;
            background-color: #FF4747;
        }
    }
</style>