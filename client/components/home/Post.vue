<template>
    <div class="blog-post hentry fbt-index-post card radius-10">
        <div v-if="post.image_url && !post.image_url.includes('https://www.diariosigloxxi.com/images/showid')" class="fbt-post-thumbnail">
            <a :href="post.url">
                <img class="post-thumbnail lazyloaded" :src="post.image_url" />
            </a>
        </div>
        <div class="fbt-post-caption card-body">
            <h3 class="post-title h4 card-title">
                <a :href="post.url">
                    <span>{{post.title.substring(0, 60)}}</span>
                </a>
            </h3>
            <div class="post-meta">
                <!-- <span class="post-author"><a href="https://www.blogger.com/profile/17166328911298692516" target="_blank" title="John Doe"><font ><font >John Doe</font></font></a></span> -->
                <span class="post-date published"><font >{{post.published_at}}</font></span>
            </div>
            <p class="post-excerpt card-text"><font >{{post.text.substring(0, 130)}}...</font></p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Post',
    props: ['post'],
    computed: {
        invalid_images() {
            return [
                'https://www.diariosigloxxi.com/images/showid/6713242',
                'https://www.diariosigloxxi.com/images/showid/6713247',
            ];
        }
    }
}
</script>
