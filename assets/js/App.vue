<script>
export default {
    data() {
        return {
            options: {},
            audioStreams: {}
        }
    },
    mounted() {
        this.options.host = this.$options.options.host;
        let splittedSources = this.$options.options.sources.split(',');
        this.options.sources = splittedSources.map((source) => {
            let tokens = source.split('|');

            return {
                'name': tokens[0],
                'freq': tokens[1],
                'link': tokens[2]
            };
        });

        this.options.sources.forEach((source) => {
            this.refreshAudioStreams(source.name);
        });
    },
    methods: {
        refreshAudioStreams(source) {
            console.log(source);
            fetch("/api/v1/broadcasted_audio_stream?source=" + source, {"method": "GET"})
                .then(response => response.json())
                .then(result => this.audioStreams[source] = result);

            setTimeout(() => {
                this.refreshAudioStreams(source);
            }, 5000);
        }
    }
}
</script>

<template>
    <div v-for="(source) in options.sources">
        <span class="isps_freq">
            <a v-bind:href="source.link" target="_blank">{{ source.freq }}</a>
        </span>
        <span class="isps_live" v-if="audioStreams[source.name] != undefined">
            <i>Live:</i>
            <p>{{ audioStreams[source.name][0].title }}</p>
        </span>
    </div>
</template>