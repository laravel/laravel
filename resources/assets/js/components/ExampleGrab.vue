<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Example of using v-model in Class-Style Vue Component</div>

                    <div class="panel-body">
                        <form>
                            <table class="table">
                                <tr>
                                    <td><input class="form-control" v-model="url" autofocus></td>
                                    <td class="col-sm-2"><input type="submit" value="Click" class="btn btn-primary" @click.prevent="grab"></td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <pre v-html="html" class="grab-msg" v-if="html"> </pre>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Model} from 'vue-property-decorator';

    @Component
    export default class extends Vue {

        @Model() readonly _url!: string;
        url = location.href;
        html = '';

        grab() {
            let now = Date.now();
            axios.get(this.url).then((res: AxiosResponse) => {
                this.html = now + ' grab the $("#app").text() from Axios Response:\n';
                this.html += $('<div>' + res.data + '</div>').find('#app').text();
            }).catch((err: AxiosError) => {
                this.html = now + ' Axios Error:\n';
                this.html += JSON.stringify(err, null, 2);
            })
        }
    }
</script>

<style>
    .grab-msg {
        max-height: 400px;
        overflow-y: scroll;
        margin: -22px 15px 15px 15px;
    }
</style>