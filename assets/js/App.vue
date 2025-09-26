<script>
import moment from 'moment';
import {Howl, Howler} from 'howler';

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
                'link': tokens[2],
                'sound': new Howl({
                    src: [tokens[2]],
                    html5: true,
                })
            };
        });

        this.options.sources.forEach((source) => {
            this.refreshAudioStreams(source.name);
        });
    },
    methods: {
        refreshAudioStreams(source) {
            fetch(this.options.host + "/api/v1/broadcasted_audio_stream?source=" + source, {"method": "GET"})
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
            fetch(this.options.host + "/api/v1/broadcasted_audio_stream?source=" + source + "&at=" + encodeURIComponent(moment(String(this.searchDate)).format('YYYY-MM-DDTHH:mm:ssZ')), {"method": "GET"})
                .then(response => response.json())
                .then(result => this.audioStreams[source] = result)
            ;
        },

        formatDate(dateToFormat) {
            if (dateToFormat) {
                return moment(String(dateToFormat)).format('DD/MM/YYYY HH:mm');
            }
        },

        playPause(event, source) {
            let player = event.target;

            source.sound.once('load', function() {
                player.classList.add('active');
            });

            if (!source.sound.playing()) {
                source.sound.play();

                if ('loaded' === source.sound.state()) {
                    player.classList.add('active');
                } else {
                    player.classList.add('load');
                }
            } else {
                source.sound.pause();
                player.classList.remove('load');
                player.classList.remove('active');
            }
        }
    }
}
</script>

<template>
    <div v-bind:class="'isps_widget '+source.name" v-for="(source) in options.sources">
        <a class="isps_light_player" v-on:click="playPause($event, source)"></a>

        <span class="isps_freq">{{ source.freq }}</span>

        <span class="isps_live" v-if="liveStream[source.name] != undefined">
            <i>Live</i>

            <div class="isps_live_title">
                <p>{{ liveStream[source.name].title }}</p>
            </div>
        </span>

        <label class="isps_show_advanced_label" v-bind:for="'isps_show_advanced_'+source.name" title="Rechercher le titre d'une musique diffusée"></label>
        <input class="isps_show_advanced_input" type="checkbox" v-bind:id="'isps_show_advanced_'+source.name" />
        <div class="isps_advanced_container">
            <form>
                <label>Rechercher par date</label>
                <input type="datetime-local" name="observed_at" v-model="searchDate" v-on:input="searchAudioStreams(source.name)"/>
            </form>

            <table v-if="audioStreams[source.name] != undefined && audioStreams[source.name].length > 0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Auteur - Titre</th>
                    </tr>
                </thead>
                <tbody >
                    <tr v-for="(audioStream) in audioStreams[source.name]">
                        <td class="isps_audio_stream_observed_at">{{ formatDate(audioStream.observedAt) }}</td>
                        <td class="isps_audio_stream_title">{{ audioStream.title }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="isps_empty_results" v-if="audioStreams[source.name] == undefined || audioStreams[source.name].length == 0">
                <p>Aucuns titre observé à cette date</p>
            </div>
        </div>
    </div>
</template>