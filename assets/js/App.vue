<script>
import moment from 'moment';

export default {
    data() {
        return {
            options: {},
            liveStream: {},
            audioStreams: {},
            searchDate: undefined
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
            fetch("/api/v1/broadcasted_audio_stream?source=" + source, {"method": "GET"})
                .then(response => response.json())
                .then(function (result) {
                    this.liveStream[source] = result[0];

                    if (undefined == this.searchDate) {
                        this.audioStreams[source] = result;
                    }
                }.bind(this))
            ;

            setTimeout(() => {
                this.refreshAudioStreams(source);
            }, 5000);
        },

        searchAudioStreams(source) {
            console.log("/api/v1/broadcasted_audio_stream?source=" + source + "&at=" + encodeURIComponent(moment(String(this.searchDate)).format('YYYY-MM-DDTHH:mm:ssZ')));

            fetch("/api/v1/broadcasted_audio_stream?source=" + source + "&at=" + encodeURIComponent(moment(String(this.searchDate)).format('YYYY-MM-DDTHH:mm:ssZ')), {"method": "GET"})
                .then(response => response.json())
                .then(result => this.audioStreams[source] = result)
            ;
        },

        formatDate(dateToFormat) {
            if (dateToFormat) {
                return moment(String(dateToFormat)).format('DD/MM/YYYY HH:mm')
            }
        }
    }
}
</script>

<template>
    <div class="isps_widget" v-for="(source) in options.sources">
        <span class="isps_freq">
            <a v-bind:href="source.link" target="_blank">{{ source.freq }}</a>
        </span>

        <span class="isps_live" v-if="liveStream[source.name] != undefined">
            <i>Live</i>
            <div class="isps_live_title">
                <p>{{ liveStream[source.name].title }}</p>
            </div>
        </span>

        <label class="isps_show_advanced_label" v-bind:for="'isps_show_advanced_'+source.name" title="Rechercher le titre d'une musique diffusée">
            <i>Rechercher le titre d'une musique diffusée sur {{ source.name }}</i>
        </label>
        <input class="isps_show_advanced_input" type="checkbox" v-bind:id="'isps_show_advanced_'+source.name" />
        <div class="isps_advanced_container">
            <form>
                <label>Rechercher à une date</label>
                <input type="datetime-local" name="observed_at" v-model="searchDate" v-on:input="searchAudioStreams(source.name)"/>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Auteur - Titre</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(audioStream) in audioStreams[source.name]">
                        <td class="isps_audio_stream_observed_at">{{ formatDate(audioStream.observedAt) }}</td>
                        <td class="isps_audio_stream_title">{{ audioStream.title }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>